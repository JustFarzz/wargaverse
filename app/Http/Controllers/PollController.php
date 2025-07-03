<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\PollComment;
use App\Models\PollCommentLike;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PollController extends Controller
{
    /**
     * Display a listing of polls
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = Poll::with(['creator', 'options'])
            ->withCount(['votes', 'comments'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        switch ($filter) {
            case 'active':
                $query->where('end_date', '>', now())
                    ->where('status', 'active');
                break;
            case 'ended':
                $query->where('end_date', '<=', now())
                    ->orWhere('status', 'closed');
                break;
            default:
                // Show all polls
                break;
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $polls = $query->paginate(10);

        // Add additional data for each poll
        $polls->getCollection()->transform(function ($poll) {
            $poll->user_has_voted = $this->hasUserVoted($poll, Auth::id());
            $poll->is_active = $this->isActive($poll);
            $poll->has_ended = $this->hasEnded($poll);
            $poll->participation_percentage = $this->getParticipationPercentage($poll);
            $poll->total_votes = $poll->votes_count;
            return $poll;
        });

        return view('polling.index', compact('polls', 'filter', 'search'));
    }

    /**
     * Show the form for creating a new poll
     */
    public function create()
    {
        $categories = [
            'umum' => 'Umum',
            'keuangan' => 'Keuangan',
            'keamanan' => 'Keamanan',
            'kebersihan' => 'Kebersihan',
            'sosial' => 'Sosial',
            'pembangunan' => 'Pembangunan'
        ];

        return view('polling.create', compact('categories'));
    }

    /**
     * Store a newly created poll
     */
    public function store(Request $request)
    {
        // Sesuaikan dengan options di form create.blade.php
        $categories = ['umum', 'keamanan', 'kebersihan', 'keuangan', 'fasilitas', 'kegiatan', 'lainnya'];

        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'category' => ['required', Rule::in($categories)],
            'end_date' => 'required|date|after:now',
            'options' => 'required|array|min:2|max:8',
            'options.*' => 'required|string|max:100|distinct',
            'allow_multiple' => 'boolean',
            'anonymous' => 'boolean',
            'notify_result' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Create poll
            $poll = Poll::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'end_date' => $request->end_date,
                'allow_multiple' => $request->boolean('allow_multiple', false),
                'anonymous' => $request->boolean('anonymous', false),
                'notify_result' => $request->boolean('notify_result', true),
                'status' => 'active',
                'created_by' => Auth::id()
            ]);

            // Create options
            foreach ($request->options as $index => $optionText) {
                if (trim($optionText)) {
                    PollOption::create([
                        'poll_id' => $poll->id,
                        'option_text' => trim($optionText),
                        'order' => $index + 1
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('polling.index')
                ->with('success', 'Polling berhasil dibuat dan dipublikasikan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat polling. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified poll
     */
    public function show(Poll $poll)
    {
        return $this->vote($poll);
    }

    /**
     * Display the specified poll for voting
     */
    public function vote(Poll $poll)
    {
        $poll->load(['creator', 'options', 'votes.user', 'votes.option']);

        // Check if user has already voted
        $userVotes = collect();
        $hasVoted = false;

        if (Auth::check()) {
            $userVotes = PollVote::where('poll_id', $poll->id)
                ->where('user_id', Auth::id())
                ->with('option')
                ->get();
            $hasVoted = $userVotes->isNotEmpty();
        }

        // Get poll statistics
        $totalVotes = PollVote::where('poll_id', $poll->id)->count();
        $uniqueVoters = PollVote::where('poll_id', $poll->id)->distinct('user_id')->count();
        $totalUsers = User::where('status', 'active')->count();
        $participationPercentage = $totalUsers > 0 ? round(($uniqueVoters / $totalUsers) * 100, 1) : 0;

        // Get results for each option
        $results = collect();
        foreach ($poll->options as $option) {
            $voteCount = PollVote::where('option_id', $option->id)->count();
            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 1) : 0;

            $results->push([
                'option' => $option,
                'votes' => $voteCount,
                'percentage' => $percentage
            ]);
        }

        // Get comments with replies
        $comments = PollComment::where('poll_id', $poll->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'likes'])
            ->latest()
            ->paginate(10);

        // Get recent participants
        $participants = PollVote::where('poll_id', $poll->id)
            ->with('user')
            ->select('user_id', 'created_at')
            ->distinct('user_id')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('polling.vote', compact(
            'poll',
            'userVotes',
            'hasVoted',
            'totalVotes',
            'uniqueVoters',
            'participationPercentage',
            'results',
            'comments',
            'participants'
        ));
    }

    /**
     * Submit a vote
     */
    public function submitVote(Request $request, Poll $poll)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Anda harus login untuk vote'], 401);
        }

        if (!$this->isActive($poll)) {
            return response()->json(['error' => 'Polling sudah berakhir'], 400);
        }

        if (!$poll->allow_multiple && $this->hasUserVoted($poll, Auth::id())) {
            return response()->json(['error' => 'Anda sudah memberikan suara'], 400);
        }

        $request->validate([
            'option_id' => $poll->allow_multiple ? 'required|array' : 'required|integer',
            'option_id.*' => 'exists:poll_options,id'
        ]);

        DB::beginTransaction();
        try {
            $optionIds = $poll->allow_multiple
                ? $request->option_id
                : [$request->option_id];

            // Remove existing votes if allow_multiple is true and user is changing vote
            if ($poll->allow_multiple && $this->hasUserVoted($poll, Auth::id())) {
                PollVote::where('poll_id', $poll->id)
                    ->where('user_id', Auth::id())
                    ->delete();
            }

            foreach ($optionIds as $optionId) {
                // Verify option belongs to this poll
                $option = PollOption::where('id', $optionId)
                    ->where('poll_id', $poll->id)
                    ->first();

                if (!$option) {
                    throw new \Exception('Invalid option selected');
                }

                PollVote::create([
                    'poll_id' => $poll->id,
                    'option_id' => $optionId,
                    'user_id' => Auth::id(),
                    'ip_address' => $request->ip()
                ]);
            }

            DB::commit();

            // Get updated results
            $results = $this->getPollResults($poll);

            return response()->json([
                'success' => true,
                'message' => 'Suara berhasil disimpan!',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal menyimpan suara: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get poll results
     */
    public function results(Poll $poll)
    {
        $results = $this->getPollResults($poll);
        $totalVotes = PollVote::where('poll_id', $poll->id)->count();
        $uniqueVoters = PollVote::where('poll_id', $poll->id)->distinct('user_id')->count();
        $totalUsers = User::where('status', 'active')->count();
        $participationPercentage = $totalUsers > 0 ? round(($uniqueVoters / $totalUsers) * 100, 1) : 0;

        // Find winner
        $winner = $results->sortByDesc('votes')->first();

        return response()->json([
            'results' => $results,
            'winner' => $winner,
            'total_votes' => $totalVotes,
            'unique_voters' => $uniqueVoters,
            'participation_percentage' => $participationPercentage
        ]);
    }

    /**
     * Add comment to poll
     */
    public function addComment(Request $request, Poll $poll)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Anda harus login untuk berkomentar'], 401);
        }

        $request->validate([
            'comment' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:poll_comments,id'
        ]);

        $comment = PollComment::create([
            'poll_id' => $poll->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'comment' => $request->comment
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Komentar berhasil ditambahkan!'
        ]);
    }

    /**
     * Like/unlike a comment
     */
    public function toggleCommentLike(Request $request, PollComment $comment)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Anda harus login'], 401);
        }

        $userId = Auth::id();
        $existingLike = PollCommentLike::where('comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            PollCommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $userId
            ]);
            $liked = true;
        }

        $likesCount = PollCommentLike::where('comment_id', $comment->id)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Delete a poll (soft delete)
     */
    public function destroy(Poll $poll)
    {
        if ($poll->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $poll->delete();

        return response()->json([
            'success' => true,
            'message' => 'Polling berhasil dihapus'
        ]);
    }

    /**
     * Get active polls for API
     */
    public function getActivePolls()
    {
        $activePolls = Poll::where('end_date', '>', now())
            ->where('status', 'active')
            ->with(['creator', 'options'])
            ->withCount('votes')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($activePolls);
    }

    /**
     * Get poll statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total_polls' => Poll::count(),
            'active_polls' => Poll::where('end_date', '>', now())->where('status', 'active')->count(),
            'ended_polls' => Poll::where('end_date', '<=', now())->orWhere('status', 'closed')->count(),
            'total_votes' => PollVote::count(),
            'recent_polls' => Poll::with('creator')->orderBy('created_at', 'desc')->limit(5)->get()
        ];

        return response()->json($stats);
    }

    // Helper methods
    private function hasUserVoted($poll, $userId)
    {
        if (!$userId)
            return false;

        return PollVote::where('poll_id', $poll->id)
            ->where('user_id', $userId)
            ->exists();
    }

    private function isActive($poll)
    {
        return $poll->status === 'active' && $poll->end_date > now();
    }

    private function hasEnded($poll)
    {
        return $poll->status === 'closed' || $poll->end_date <= now();
    }

    private function getParticipationPercentage($poll)
    {
        $uniqueVoters = PollVote::where('poll_id', $poll->id)->distinct('user_id')->count();
        $totalUsers = User::where('status', 'active')->count();

        return $totalUsers > 0 ? round(($uniqueVoters / $totalUsers) * 100, 1) : 0;
    }

    private function getPollResults($poll)
    {
        $totalVotes = PollVote::where('poll_id', $poll->id)->count();
        $results = collect();

        foreach ($poll->options as $option) {
            $voteCount = PollVote::where('option_id', $option->id)->count();
            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 1) : 0;

            $results->push([
                'option' => $option,
                'votes' => $voteCount,
                'percentage' => $percentage
            ]);
        }

        return $results;
    }
}