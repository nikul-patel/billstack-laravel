<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\Business;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorizeSuperAdmin();
        $users = User::query()->with('business')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorizeSuperAdmin();
        $businesses = Business::orderBy('name')->get();

        return view('admin.users.create', compact('businesses'));
    }

    public function store(AdminUserRequest $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user): View
    {
        $this->authorizeSuperAdmin();
        $businesses = Business::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'businesses'));
    }

    public function update(AdminUserRequest $request, User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $data = $request->validated();
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    protected function authorizeSuperAdmin(): void
    {
        if (! $this->userIsSuperAdmin()) {
            abort(403);
        }
    }
}
