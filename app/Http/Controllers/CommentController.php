<?php
namespace App\Http\Controllers;

use App\Repositories\CommentRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Comment;
use App\Repositories\Criteria\WorkerOrders;
use yajra\Datatables\Datatables;
use App\Events\ApprovalComment;
use App\Events\ApprovedComment;
use Symfony\Component\HttpFoundation\Cookie;

class CommentController extends Controller
{
    private $commentRepository;

    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    public function __construct(CommentRepository $commentRepository, AttachmentRepository $attachmentRepository,OrderRepository $orderRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Sentinel::check()->inRole('administrator') || Sentinel::check()->inRole('sub-admin')) {
            $newComments = $this->commentRepository->getCountPendingComments();
            $response = new \Illuminate\Http\Response(view('comments.index'));
            return $response->withCookie(cookie('comments', $newComments));
        } else {
            return view('comments.index');
        }
    }

    /**
     * Display a listing of comments of specific order.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function getComments(Request $request)
    {
        $comments = $this->commentRepository->getCommentsOrder($request->orderId, $request->namespace, Sentinel::check()->id);
        if ($request->namespace === Comment::PRIVATE_CHANNEL) {
        return Datatables::of($comments)
            ->addRowData('actions', function ($result) {
                return [];
            })
            ->addColumn('col_comment_file', function ($result) {
                if (sizeof($result->attachment) > 0) {
                    return '<div>'.$result->attachment[0]->getlabel().'</div><br><a href="' . $result->attachment[0]->path . '" target="_blank">' . $result->attachment[0]->name . '</a>';
                } else {
                    return 'No attachment';
                };
            }, false)
            ->addColumn('col_comment_data', function ($result) {
                if ($result->user->id !== Sentinel::check()->id) {
                    if ($result->user->roles[0]->slug === 'administrator') {
                        return '<br><div class="blue-text text-darken-2">(Admin) </div>'.$result->user->first_name.' '.$result->user->last_name.'<br>'.$this->setTime($result->created_at);
                    } else {
                        return '<br>'.$result->user->first_name.' '.$result->user->last_name.'<br>'.$this->setTime($result->created_at);
                    }
                } else {
                    return '<br><div class="blue-text text-darken-2">(You)</div><br>'.$this->setTime($result->created_at);
                }
            }, false)
            ->addColumn('col_comment_content', function ($result) {
                return '<div class="card-panel teal blue lighten-4">'.$result->content.'</div>';
            }, false)
            ->make(true);
        } else {
            return Datatables::of($comments)
            ->addRowData('actions', function ($result) {
                return [];
            })
            ->addColumn('col_comment_file', function ($result) {
                if (sizeof($result->attachment) > 0) {
                    return '<div>'.$result->attachment[0]->getlabel().'</div><br><a href="' . $result->attachment[0]->path . '" target="_blank">' . $result->attachment[0]->name . '</a>';
                } else {
                    return 'No attachment';
                };
            }, false)
            ->addColumn('col_comment_data', function ($result) {
                    $classStyle = '';
                    switch ($result->user->roles[0]->slug) {
                        case "adminitrator":
                            $classStyle = 'class="red-text text-lighten-2"';
                            break;
                        case "worker":
                            $classStyle = 'class="purple-text text-lighten-1"';
                            break;
                        case "customer":
                            $classStyle = 'class="green-text text-darken-4"';
                            break;
                        default:
                            $classStyle = 'class="grey-text text-darken-4"';
                    }
                if ($result->user->id !== Sentinel::check()->id) {
                    if (Sentinel::check()->roles[0]->slug === "worker" && $result->user->roles[0]->slug === "customer") {
                        return '<div '.$classStyle.'>appraiser ('.$result->user->id.')</div><br>'.$this->setTime($result->created_at);
                    } else {
                        if ($result->user->roles[0]->slug === "customer") {
                            return '<div '.$classStyle.'>appraiser</div>'.$result->user->first_name.' '.$result->user->last_name.'<br>'.$this->setTime($result->created_at);
                        } else {
                            return '<div '.$classStyle.'>'.$result->user->roles[0]->slug.'</div>'.$result->user->first_name.' '.$result->user->last_name.'<br>'.$this->setTime($result->created_at);
                        }
                    }
                } else {
                    return '<br><div '.$classStyle.'>'.$result->user->roles[0]->slug.' (You)</div><br>'.$this->setTime($result->created_at);
                }
            }, false)
            ->addColumn('col_comment_content', function ($result) {
                return '<div class="card-panel teal blue lighten-4">'.$result->content.'</div>';
            }, false)
            ->make(true);
        }
    }

    /**
     * Save a new comment
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function createComment(Request $request)
    {
        $user = Sentinel::check();
        $data = [
            'user_id' => Sentinel::check()->id,
            'namespace' => $request->namespace,
            'parent_id' => 0,
            'order_id' => $request->orderId,
            'content' => $request->content,
            'role_id' => $user->roles[0]->id
        ];

        $comment = $this->commentRepository->createComments($data, $user);

        return response()->json($comment);
    }

    /**
     * Display a list of pending comments
     */
    public function getPending()
    {
        $comments = $this->commentRepository->getPendingComments();
        return Datatables::of($comments)
            ->addRowData('actions', function ($result) {
                $actions = [
                    'Approve' => [
                        'link' => route('admin:comment.approve', ['comment_id' => $result->id,
                        'by' => Sentinel::check()->id]),
                        'method' => 'PUT'
                    ],
                    'Edit' => [
                        'link' => route('admin:comment.edit', ['comment_id' => $result->id]),
                        'ajax' => false
                    ],
                    'Delete' => [
                        'link' => route('admin:comment.delete', ['comment_id' => $result->id]),
                        'method' => 'DELETE'
                    ]
                ];
                return $actions;
            })
            ->addColumn('col_comment_worker', function ($result) {
                return $result->user->first_name.' '.$result->user->last_name;
            }, false)
            ->addColumn('col_comment_order', function ($result) {
                return $result->order_id;
            }, false)
            ->addColumn('col_comment_content', function ($result) {
                return '<div class="card-panel teal blue lighten-4">'.$result->content.'</div>';
            }, false)
            ->make(true);
    }

    public function approve($comment_id, $by, Request $request)
    {
        $comment = Comment::pending()->find($comment_id);
        $comment->markApproved();
        $comment->moderated_by = $by;
        $comment->save();
        event(new ApprovedComment($comment->id));

        $cookie = intval($request->cookie('comments'));
        return response()->json([
            'success' => true,
            'message' => 'Comment successfully approved',
        ], SymfonyResponse::HTTP_OK)->withCookie(cookie('comments', ($cookie-1)));
    }

    /**
     * Show the form for editing the specified comment.
     *
     * @param  $comment_id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($comment_id)
    {
        $comment = Comment::pending()->find($comment_id);
        if (Sentinel::check()->inRole('administrator') || Sentinel::check()->inRole('sub-admin')) {
            $newComments = $this->commentRepository->getCountPendingComments();
            $response = new \Illuminate\Http\Response('comments.edit', compact('comment'));
            return $response->withCookie(cookie('comments', $newComments));
        } else {
            return view('comments.edit', compact('comment'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WorkerGroup                $group
     * @param WorkerGroupRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     * @internal param int $id
     *
     */
    public function update($comment_id, Request $request)
    {
        try {
            $comment = Comment::pending()->find($comment_id);
            $comment->content = $request->content;
            $comment->save();
            return response()->json([
                'success' => true
            ], SymfonyResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error, try again later',
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $comment_id
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($comment_id, Request $request)
    {
        $comment = Comment::pending()->find($comment_id);
        $comment->delete();
        $cookie = intval($request->cookie('comments'));
        return response()->json([
            'success' => true,
            'message' => 'Comment successfully approved',
        ], SymfonyResponse::HTTP_OK)->withCookie(cookie('comments', ($cookie-1)));
    }

    public function setTime($time) {
        if (Sentinel::check()->inRole('administrator') || Sentinel::check()->inRole('sub-admin')) {
            return date_format($time, 'jS F Y\, g:ia');
        } else {
            return $time->diffForHumans();
        }
    }
}