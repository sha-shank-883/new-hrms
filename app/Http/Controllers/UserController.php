<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $query = User::with('roles');
        
        // Filter by role if requested
        if ($request->has('role_id')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('id', $request->role_id);
            });
        }
        
        $users = $query->orderBy('name')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return View
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Only super admin can set admin flag
        if (Auth::user()->hasRole('super-admin')) {
            $validated['is_admin'] = $request->has('is_admin');
        } else {
            $validated['is_admin'] = false;
        }

        $user = User::create($validated);
        $user->roles()->attach($request->roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id): View
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  Request  $request
     * @param  User  $user
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Only super admin can update admin flag
        if (Auth::user()->hasRole('super-admin')) {
            $validated['is_admin'] = $request->has('is_admin');
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Sync roles
        $user->roles()->sync($request->roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  User  $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        
        // Prevent deleting yourself
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}