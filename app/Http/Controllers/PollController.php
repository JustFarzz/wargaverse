<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function adminCreate()
    {
        return view('admin.createpolling');
    }
    public function index()
    {
        $user = Auth::user();

        // Get all pollings with related data
        $pollings = Poll::with(['options', 'votes', 'user'])
            ->withCount(['votes as total_votes'])
            ->where('rt', $user->rt)
            ->where('rw', $user->rw)
            ->orderBy('created_at', 'desc')
            ->get();

        // Add additional data to each polling
        $pollings->each(function ($polling) {
            // Calculate total participants
            $polling->total_participants = $polling->votes->unique('user_id')->count();

            // Check if current user has voted
            $polling->user_has_voted = $polling->votes->where('user_id', Auth::id())->count() > 0;

            // Determine status
            $polling->status = $polling->end_date < now() ? 'ended' : 'active';

            // Calculate participation percentage (assuming total users in RT)
            $totalUsers = User::where('rt', $polling->rt)->where('rw', $polling->rw)->count();
            $polling->participation_percentage = $totalUsers > 0 ?
                round(($polling->total_participants / $totalUsers) * 100, 1) : 0;
        });

        return view('polling.index', compact('pollings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('polling.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'category' => 'required|string|in:umum,keamanan,kebersihan,keuangan,fasilitas,kegiatan,lainnya',
            'end_date' => 'required|date|after:now',
            'options' => 'required|array|min:2|max:8',
            'options.*' => 'required|string|max:100|distinct',
            'allow_multiple' => 'boolean',
            'anonymous' => 'boolean',
            'notify_result' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Create polling
            $polling = Poll::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'end_date' => $request->end_date,
                'allow_multiple' => $request->has('allow_multiple'),
                'anonymous' => $request->has('anonymous'),
                'notify_result' => $request->has('notify_result'),
                'user_id' => $user->id,
                'rt' => $user->rt,
                'rw' => $user->rw,
                'status' => 'active',
            ]);

            // Create poll options
            foreach ($request->options as $index => $option) {
                if (trim($option) !== '') {
                    PollOption::create([
                        'poll_id' => $polling->id,
                        'option_text' => trim($option),
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('polling.index')
                ->with('success', 'Polling berhasil dibuat dan dipublikasikan!');

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error creating poll: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat polling. Silakan coba lagi.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'category' => 'required|string|in:umum,keamanan,kebersihan,keuangan,fasilitas,kegiatan,lainnya',
            'end_date' => 'required|date|after:now',
            'options' => 'required|array|min:2|max:8',
            'options.*' => 'required|string|max:100|distinct',
            'allow_multiple' => 'boolean',
            'anonymous' => 'boolean',
            'notify_result' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Create polling
            $polling = Poll::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'end_date' => $request->end_date,
                'allow_multiple' => $request->has('allow_multiple'),
                'anonymous' => $request->has('anonymous'),
                'notify_result' => $request->has('notify_result'),
                'user_id' => $user->id,
                'rt' => $user->rt,
                'rw' => $user->rw,
                'status' => 'active',
            ]);

            // Create poll options
            foreach ($request->options as $index => $option) {
                if (trim($option) !== '') {
                    PollOption::create([
                        'poll_id' => $polling->id,
                        'option_text' => trim($option),
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('polling.index')
                ->with('success', 'Polling berhasil dibuat dan dipublikasikan!');

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error creating poll: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat polling. Silakan coba lagi.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Poll $polling)
    {
        // Check if user can access this polling (same RT/RW)
        $user = Auth::user();
        if ($polling->rt !== $user->rt || $polling->rw !== $user->rw) {
            return redirect()->route('polling.index')
                ->withErrors(['error' => 'Anda tidak dapat mengakses polling ini.']);
        }

        // Load related data
        $polling->load(['options.votes', 'user', 'votes.user', 'votes.pollOption']);

        // Check if user has voted
        $userVote = $polling->votes->where('user_id', Auth::id())->first();

        // Get voting statistics
        $totalVotes = $polling->votes->count();
        $totalParticipants = $polling->votes->unique('user_id')->count();

        // Calculate option statistics
        $optionStats = $polling->options->map(function ($option) use ($totalVotes) {
            $optionVotes = $option->votes->count();
            return [
                'id' => $option->id,
                'text' => $option->option_text,
                'votes' => $optionVotes,
                'percentage' => $totalVotes > 0 ? round(($optionVotes / $totalVotes) * 100, 1) : 0,
            ];
        });

        // Get total users in RT/RW for participation calculation
        $totalUsers = User::where('rt', $polling->rt)->where('rw', $polling->rw)->count();

        return view('polling.vote', compact('polling', 'userVote', 'totalVotes', 'totalParticipants', 'optionStats', 'totalUsers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poll $polling)
    {
        $user = Auth::user();

        // Only allow editing if user is the creator and polling is still active
        if (
            $polling->user_id !== $user->id || $polling->end_date < now() ||
            $polling->rt !== $user->rt || $polling->rw !== $user->rw
        ) {
            return redirect()->route('polling.index')
                ->withErrors(['error' => 'Anda tidak dapat mengedit polling ini.']);
        }

        $polling->load('options');

        return view('polling.edit', compact('polling'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Poll $polling)
    {
        $user = Auth::user();

        // Only allow updating if user is the creator and polling is still active
        if (
            $polling->user_id !== $user->id || $polling->end_date < now() ||
            $polling->rt !== $user->rt || $polling->rw !== $user->rw
        ) {
            return redirect()->route('polling.index')
                ->withErrors(['error' => 'Anda tidak dapat mengupdate polling ini.']);
        }

        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'category' => 'required|string|in:umum,keamanan,kebersihan,keuangan,fasilitas,kegiatan,lainnya',
            'end_date' => 'required|date|after:now',
            'options' => 'required|array|min:2|max:8',
            'options.*' => 'required|string|max:100|distinct',
            'allow_multiple' => 'boolean',
            'anonymous' => 'boolean',
            'notify_result' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Update polling
            $polling->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'end_date' => $request->end_date,
                'allow_multiple' => $request->has('allow_multiple'),
                'anonymous' => $request->has('anonymous'),
                'notify_result' => $request->has('notify_result'),
            ]);

            // Delete existing options (only if no votes exist)
            if ($polling->votes->count() === 0) {
                $polling->options()->delete();

                // Create new options
                foreach ($request->options as $index => $option) {
                    if (trim($option) !== '') {
                        PollOption::create([
                            'poll_id' => $polling->id,
                            'option_text' => trim($option),
                            'order' => $index + 1,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('polling.show', $polling)
                ->with('success', 'Polling berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengupdate polling. Silakan coba lagi.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poll $polling)
    {
        $user = Auth::user();

        // Only allow deletion if user is the creator
        if ($polling->user_id !== $user->id || $polling->rt !== $user->rt || $polling->rw !== $user->rw) {
            return redirect()->route('polling.index')
                ->withErrors(['error' => 'Anda tidak dapat menghapus polling ini.']);
        }

        DB::beginTransaction();

        try {
            // Delete all related data
            $polling->votes()->delete();
            $polling->options()->delete();
            $polling->delete();

            DB::commit();

            return redirect()->route('polling.index')
                ->with('success', 'Polling berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('polling.index')
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus polling. Silakan coba lagi.']);
        }
    }

    /**
     * Submit a vote for the specified polling.
     */
    public function vote(Request $request, Poll $polling)
    {
        $user = Auth::user();

        // Check if user can vote (same RT/RW)
        if ($polling->rt !== $user->rt || $polling->rw !== $user->rw) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak dapat voting untuk polling ini.'], 403);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Anda tidak dapat voting untuk polling ini.']);
        }

        // Check if polling is still active
        if ($polling->end_date < now()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Polling sudah berakhir.'], 403);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Polling sudah berakhir.']);
        }

        // Check if user has already voted
        $existingVote = $polling->votes->where('user_id', $user->id)->first();
        if ($existingVote) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda sudah memberikan suara untuk polling ini.'], 403);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Anda sudah memberikan suara untuk polling ini.']);
        }

        $request->validate([
            'options' => 'required|array|min:1',
            'options.*' => 'exists:poll_options,id',
        ]);

        // Validate that selected options belong to this poll
        $validOptions = PollOption::where('poll_id', $polling->id)
            ->whereIn('id', $request->options)
            ->pluck('id')
            ->toArray();

        if (count($validOptions) !== count($request->options)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pilihan tidak valid.'], 422);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Pilihan tidak valid.']);
        }

        // Check if multiple selection is allowed
        if (!$polling->allow_multiple && count($request->options) > 1) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Hanya boleh memilih satu opsi.'], 422);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Hanya boleh memilih satu opsi.']);
        }

        DB::beginTransaction();

        try {
            // Create votes for each selected option
            foreach ($request->options as $optionId) {
                Vote::create([
                    'poll_id' => $polling->id,
                    'poll_option_id' => $optionId,
                    'user_id' => $user->id,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                    'ip_address' => $request->ip(),
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Suara Anda berhasil disimpan!'], 200);
            }

            return redirect()->route('polling.show', $polling)
                ->with('success', 'Suara Anda berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error voting: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat menyimpan suara.'], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan suara. Silakan coba lagi.']);
        }
    }

    /**
     * Get polling statistics for dashboard or reports.
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'total_pollings' => Poll::where('rt', $user->rt)->where('rw', $user->rw)->count(),
            'active_pollings' => Poll::where('rt', $user->rt)->where('rw', $user->rw)->where('end_date', '>', now())->count(),
            'ended_pollings' => Poll::where('rt', $user->rt)->where('rw', $user->rw)->where('end_date', '<=', now())->count(),
            'total_votes' => Vote::where('rt', $user->rt)->where('rw', $user->rw)->count(),
            'total_participants' => Vote::where('rt', $user->rt)->where('rw', $user->rw)->distinct('user_id')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Load more participants via AJAX
     */
    public function loadMoreParticipants(Request $request, Poll $polling)
    {
        $user = Auth::user();

        // Check access
        if ($polling->rt !== $user->rt || $polling->rw !== $user->rw) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);

        $participants = $polling->votes()
            ->with('user')
            ->select('user_id', 'created_at')
            ->distinct('user_id')
            ->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $participantData = $participants->map(function ($vote) {
            return [
                'name' => $vote->user->name,
                'avatar_url' => $vote->user->avatar_url ?? 'https://images.unsplash.com/photo-1494790108755-2616b2e2e5cc?w=40&h=40&fit=crop&crop=face',
                'vote_time' => $vote->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'participants' => $participantData,
            'has_more' => $participants->count() === $limit
        ]);
    }
}