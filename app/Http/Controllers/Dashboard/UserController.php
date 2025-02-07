<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\HospitalService;
use App\Services\UserService;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private UserService $userService, private HospitalService $hospitalService)
    {
    }

    public function list()
    {
        $data = [
            'users' => $this->userService->filter(),
            'userTypes' => UserType::getAll(),
        ];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/User/List', $data);
    }

    public function create()
    {
        $data = [];

        if (!auth()->user()->hasHospital()) {
            $data['hospitals'] = $this->hospitalService->getAll();
        }

        return Inertia::render('Dashboard/User/Create', $data);
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->store($request->validated(), UserType::ADMIN);

        session()->flash('toast.success', trans('messages.user.created'));

        return to_route('dashboard.user.list');
    }

    public function edit(User $user)
    {
        return Inertia::render('Dashboard/User/Edit', [
            'user' => $user,
            'hospitals' => $this->hospitalService->getAll(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());

        session()->flash('toast.success', trans('messages.user.updated'));

        return to_route('dashboard.user.list');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        session()->flash('toast.success', trans('messages.user.updated'));
    }
}
