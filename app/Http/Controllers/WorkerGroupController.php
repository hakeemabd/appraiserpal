<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\WorkerAssignmentRequest;
use App\Http\Requests\WorkerGroupRequest;
use App\Models\WorkerGroup;
use App\Repositories\WorkerGroupRepository;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use yajra\Datatables\Datatables;

class WorkerGroupController extends Controller
{
    /**
     * @var WorkerGroupRepository
     */
    protected $workerGrouprepository;

    public function __construct(WorkerGroupRepository $wg)
    {
        $this->workerGrouprepository = $wg;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.group.index');
    }

    public function getGroups()
    {
        return Datatables::of($this->workerGrouprepository->allSorted())
            ->addRowData('actions', function ($model) {
                return [
                    'assigned' => [
                        'link' => route('admin:workerGroup.assigned', ['workerGroup' => $model]),
                        'ajax' => false,
                        'text' => 'Assigned users',
                    ],
                    'edit' => [
                        'link' => route('admin:workerGroup.edit', ['workerGroup' => $model]),
                        'ajax' => false,
                    ],
                    'delete' => [
                        'link' => route('admin:workerGroup.destroy', ['workerGroup' => $model]),
                        'confirm' => true,
                        'confirm-type' => 'remove',
                    ],
                ];
            })
            ->make(true);
    }

    public function assignedUsers(WorkerGroup $model)
    {
        $workers = Sentinel::findRoleBySlug('worker')->users()->whereNotIn('users.id', function ($query) use ($model) {
            $query->select('worker_groups.user_id')->distinct()->from('worker_groups')
                ->where('worker_groups.group_id', $model->id);
        })->get();

        return view('user.group.assigned', compact('model', 'workers'));
    }

    /**
     * @param WorkerGroup $group
     *
     * @return mixed
     */
    public function assignedUsersData(WorkerGroup $group, Request $request)
    {

        return Datatables::of($group->workers)
            ->addRowData('actions', function ($model) use ($group) {
                $actions['edit'] = [
                    'handler' => 'popup',
                ];
                $actions['delete'] = [
                    'link' => route('admin:workerGroup.unassign', ['workerGroup' => $group, 'user' => $model]),
                    'confirm' => true,
                    'confirm-type' => 'remove',
                    'method' => 'PUT',
                ];

                return $actions;
            })
            ->addColumn('fee', function ($model) {
                return $model->pivot->fee;
            })
            ->addColumn('second_fee', function ($model) {
                return $model->pivot->second_fee;
            })
            ->addColumn('first_turnaround', function ($model) {
                return $model->pivot->first_turnaround;
            })
            ->addColumn('next_turnaround', function ($model) {
                return $model->pivot->next_turnaround;
            })
            ->setRowId('id')
            ->make(true);
    }

    public function assign(WorkerAssignmentRequest $request, WorkerGroup $group)
    {
        $worker = $group->workers()->where('user_id', $request->user_id)->first();
        if ($worker) {
            foreach ($request->except(['user_id']) as $field => $value) {
                $worker->pivot->{$field} = $value;
            }
            $worker->pivot->save();
        } else {
            $group->workers()->attach($request->user_id, $request->except(['user_id']));
        }
        return response()->json();
    }

    public function unassign(WorkerGroup $group, User $worker)
    {
        $group->workers()->detach($worker);
        return response()->json();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.group.entity');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkerGroupRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WorkerGroupRequest $request)
    {
        $this->workerGrouprepository->create($request->only('name', 'sort'));
        Cache::forget('groups');
        return response()->json(['redirect' => route('admin:workerGroup.index')]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkerGroup $model)
    {
        return view('user.group.entity', compact('model'));
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
    public function update(WorkerGroup $group, WorkerGroupRequest $request)
    {
        $this->workerGrouprepository->update($request->only('name', 'sort'), $group);
        Cache::forget('groups');
        return response()->json(['redirect' => route('admin:workerGroup.index')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->workerGrouprepository->delete($id);
        return response()->json();
    }
}
