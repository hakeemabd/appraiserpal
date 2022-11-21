<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use yajra\Datatables\Datatables;

class CrudController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index($role = 'customer')
    {
        if ($role == 'sub-admin' && \Sentinel::check()->inRole('sub-admin')) {
            return redirect(url('/'));
        }
        return view('user.index', compact('role'));
    }

    public function getUsers($role = 'customer')
    {
        try {
            $users = Sentinel::findRoleBySlug($role)->users();
            return Datatables::of($users)
                ->addColumn('action', function ($model) {
                    return '
                    <a href="/user/' . $model->id . '/edit"><i class="material-icons">create</i></a>
                    <a href="#" onclick="deleteModel(\'' . $model->id . '\')"><i class="material-icons">delete</i></a>';
                })
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function create($role)
    {
        if (view()->exists('user.' . $role)) {
            return view('user.' . $role, compact('role'));
        }
        return redirect(route('admin:usersList'));
    }

    /**
     * @param UserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(UserRequest $request)
    {

        $role = Sentinel::findRoleBySlug($request->role);
        if ($role) {
            $user = Sentinel::registerAndActivate($request->except('role'));
            $role->users()->attach($user);
        } else {
            return response()->json(['message' => 'Wrong role!'], SymfonyResponse::HTTP_FORBIDDEN);
        }

        return response()->json(['redirect' => route('admin:usersList', ['role' => $request->role])]);
    }

    public function show($id)
    {
        return 'Not implemented';
    }

    public function edit($model)
    {
        $role = 'administrator';
        if ($model->inRole('worker')) {
            $role = 'worker';
        } elseif ($model->inRole('customer')) {
            $role = 'customer';
        }

        if (view()->exists('user.' . $role)) {
            return view('user.' . $role, compact('model', 'role'));
        }
        return redirect(route('admin:usersList'));
    }

    public function update(User $user, UserRequest $request)
    {
        if ($request->password) {
            Sentinel::update($user, $request->all());
        } else {
            $user->fill($request->except('password'));
            $user->save();
        }

        return response()->json(['redirect' => route('admin:usersList', ['role' => $request->role])]);
    }

    public function destroy(User $user)
    {
        $this->userRepository->delete($user);

        return response()->json(['success' => true]);
    }
}
