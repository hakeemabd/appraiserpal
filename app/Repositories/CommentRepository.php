<?php
namespace App\Repositories;

use App\Models\Comment;
use App\Events\ApprovalComment;
use App\Events\ApprovedComment;
use Illuminate\Support\Facades\Response;

class CommentRepository extends BaseRepository
{
	public function model()
    {
        return Comment::class;
    }

	public function getCommentsOrder($orderId, $namespace, $user) {
		return $this->model
            ->withAnyStatus()
            ->where('order_id', '=', $orderId)
            ->where('namespace', '=', $namespace)
            ->where(function ($query) use($user){
                $query->orWhere('status', '=', 1)
                      ->orWhere('user_id', '=', $user);
            })
            ->get();
	}

    public function getPendingComments() {
        return $this->model->pending()->get();
    }

    public function createComments($data, $user) {
        $comment = $this->model->create($data);
         if ($user->inRole('administrator') || $user->inRole('sub-admin')) {
            $comment->markApproved();
            event(new ApprovedComment($comment->id));
         } else {
            if ($user->auto_comments) {
                $comment->markApproved();
                event(new ApprovedComment($comment->id));
            } else {
                $comment->markPending();
                event(new ApprovalComment($comment->id));
            }
         }
         return $comment;
    }

    public function getCountPendingComments() {
        $newComments = 0;
        $newComments = Comment::pending()->count();
        
        return $newComments;
    }
}