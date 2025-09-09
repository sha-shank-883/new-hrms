<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show the login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        \Log::info('=== LOGIN ATTEMPT ===');
        \Log::info('Email:', ['email' => $request->email]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', ['errors' => $validator->errors()->all()]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        \Log::debug('Authentication attempt', [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            \Log::info('=== AUTHENTICATION SUCCESSFUL ===');
            \Log::info('User details:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at
            ]);

            // Load roles with debug info
            $roles = $user->roles()->get();
            $roleNames = $user->getRoleNames();
            \Log::debug('User roles:', [
                'roles_count' => $roles->count(),
                'role_names' => $roleNames,
                'raw_roles' => $roles->toArray()
            ]);

            // Role checks
            $hasSuperAdmin = $user->hasRole('super_admin');
            $hasHrManager = $user->hasRole('hr_manager');
            \Log::debug('Role checks:', [
                'has_super_admin' => $hasSuperAdmin,
                'has_hr_manager' => $hasHrManager,
                'all_roles' => $user->getRoleNames()->toArray()
            ]);

            // Ensure session is saved before redirect
            session()->save();

            // Redirect based on role
            if ($hasSuperAdmin) {
                \Log::info('Redirecting to admin dashboard', [
                    'user_id' => $user->id,
                    'roles' => $user->getRoleNames()->toArray()
                ]);
                return redirect()->route('admin.dashboard')
                    ->with('status', 'Welcome back, Administrator!');
            } elseif ($hasHrManager) {
                \Log::info('Redirecting to HR dashboard', [
                    'user_id' => $user->id,
                    'roles' => $user->getRoleNames()->toArray()
                ]);
                return redirect()->route('hr.dashboard')
                    ->with('status', 'Welcome to HR Dashboard!');
            } elseif ($user->hasRole('department_manager')) {
                \Log::info('Redirecting to department dashboard', [
                    'user_id' => $user->id,
                    'roles' => $user->getRoleNames()->toArray()
                ]);
                return redirect()->route('department.dashboard')
                    ->with('status', 'Welcome to Department Dashboard!');
            } elseif ($user->hasRole('employee')) {
                \Log::info('Redirecting to employee dashboard', [
                    'user_id' => $user->id,
                    'roles' => $user->getRoleNames()->toArray()
                ]);
                return redirect()->route('employee.dashboard');
            }

            \Log::warning('No matching role found, using default dashboard');
            return redirect()->route('dashboard');
        }

        return redirect()->back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    /**
     * Show the registration form
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        // Assign default employee role
        $user->assignRole('employee');
        
        Auth::login($user);

        return redirect()->route('employee.dashboard');
    }

    /**
     * Handle logout request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();
        \Log::info('Logout initiated', ['user_id' => $userId]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        \Log::info('Logout completed', ['user_id' => $userId]);

        return redirect('/');
    }
}
