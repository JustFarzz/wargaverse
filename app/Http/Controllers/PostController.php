<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class PostController extends Controller
{
    /**
     * Display a listing of posts
     */
    public function index(Request $request)
    {
        $query = Post::with(['user', 'firstImage'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by RT if provided
        if ($request->has('rt') && $request->rt !== 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('rt', $request->rt);
            });
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $posts = $query->paginate(10);

        return view('timeline.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        return view('timeline.create');
    }

    /**
     * Store a newly created post in storage
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:1000',
            'category' => 'required|in:jual,jasa,info',
            'price' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Additional validation for contact info based on category
        if (in_array($request->category, ['jual', 'jasa'])) {
            $request->validate([
                'phone' => 'required_without:whatsapp|nullable|string|max:20',
                'whatsapp' => 'required_without:phone|nullable|string|max:20',
            ], [
                'phone.required_without' => 'Nomor telepon atau WhatsApp harus diisi untuk kategori ini.',
                'whatsapp.required_without' => 'Nomor telepon atau WhatsApp harus diisi untuk kategori ini.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Create the post
            $post = new Post();
            $post->user_id = Auth::id();
            $post->title = $request->title;
            $post->content = $request->content;
            $post->category = $request->category;
            $post->price = $request->price;
            $post->phone = $request->phone;
            $post->whatsapp = $request->whatsapp;
            $post->status = 'active';
            $post->save();

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUploads($request->file('images'), $post);
            }

            DB::commit();

            return redirect()->route('timeline.index')->with('success', 'Posting berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat posting. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified post
     */
    public function show(Post $post)
    {
        // Load user and images relationships
        $post->load(['user', 'images']);

        // Increment views count
        $post->incrementViews();

        return view('timeline.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        // Check if user can edit this post
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('timeline.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit posting ini.');
        }

        // Load existing images
        $post->load('images');

        return view('timeline.edit', compact('post'));
    }

    /**
     * Update the specified post in storage
     */
    public function update(Request $request, Post $post)
    {
        // Check if user can update this post
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('timeline.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengupdate posting ini.');
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:1000',
            'category' => 'required|in:jual,jasa,info',
            'price' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:post_images,id'
        ]);

        // Additional validation for contact info based on category
        if (in_array($request->category, ['jual', 'jasa'])) {
            $request->validate([
                'phone' => 'required_without:whatsapp|nullable|string|max:20',
                'whatsapp' => 'required_without:phone|nullable|string|max:20',
            ], [
                'phone.required_without' => 'Nomor telepon atau WhatsApp harus diisi untuk kategori ini.',
                'whatsapp.required_without' => 'Nomor telepon atau WhatsApp harus diisi untuk kategori ini.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update post data
            $post->title = $request->title;
            $post->content = $request->content;
            $post->category = $request->category;
            $post->price = $request->price;
            $post->phone = $request->phone;
            $post->whatsapp = $request->whatsapp;
            $post->save();

            // Handle image deletions
            if ($request->has('delete_images')) {
                $imagesToDelete = PostImage::whereIn('id', $request->delete_images)
                    ->where('post_id', $post->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $image->delete();
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $currentImageCount = $post->images()->count();
                $newImageCount = count($request->file('images'));

                if ($currentImageCount + $newImageCount > 5) {
                    throw new \Exception('Maksimal 5 gambar diizinkan per posting.');
                }

                $this->handleImageUploads($request->file('images'), $post);
            }

            DB::commit();

            return redirect()->route('timeline.show', $post)
                ->with('success', 'Posting berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified post from storage
     */
    public function destroy(Post $post)
    {
        // Check if user can delete this post
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('timeline.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus posting ini.');
        }

        try {
            DB::beginTransaction();

            // Delete all images associated with the post
            $post->images()->delete();

            // Delete the post
            $post->delete();

            DB::commit();

            return redirect()->route('timeline.index')
                ->with('success', 'Posting berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus posting. Silakan coba lagi.');
        }
    }

    /**
     * Handle image uploads for a post
     */
    private function handleImageUploads($images, Post $post)
    {
        // Create posts directory if it doesn't exist
        if (!Storage::exists('posts')) {
            Storage::makeDirectory('posts');
        }

        $order = $post->images()->count();

        foreach ($images as $image) {
            // Generate unique filename
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Store the image
            $path = $image->storeAs('posts', $filename, 'public');

            // Create database record
            PostImage::create([
                'post_id' => $post->id,
                'filename' => $filename,
                'original_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getMimeType(),
                'size' => $image->getSize(),
                'order' => $order++
            ]);
        }
    }

    /**
     * Delete a specific image from a post
     */
    public function deleteImage(PostImage $image)
    {
        // Check if user can delete this image
        if ($image->post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $image->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete image'], 500);
        }
    }

    /**
     * API endpoint to get posts (for AJAX requests)
     */
    public function apiIndex(Request $request)
    {
        $query = Post::with(['user', 'firstImage'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('rt') && $request->rt !== 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('rt', $request->rt);
            });
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $posts = $query->paginate(10);

        return response()->json([
            'posts' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'total' => $posts->total()
        ]);
    }

    /**
     * Admin methods
     */
    public function adminIndex()
    {
        $posts = Post::with(['user', 'firstImage'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function hide(Post $post)
    {
        $post->update(['status' => 'hidden']);
        return redirect()->back()->with('success', 'Posting berhasil disembunyikan.');
    }

    public function showPost(Post $post)
    {
        $post->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Posting berhasil ditampilkan kembali.');
    }
}