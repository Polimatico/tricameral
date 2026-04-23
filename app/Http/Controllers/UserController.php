<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('login.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('login.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'nickname' => $request->validated('nickname') ?: null,
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function showEditProfile(): View
    {
        return view('user.profile.edit', ['user' => Auth::user()]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->fill([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'nickname' => $request->validated('nickname') ?: null,
            'show_name' => $request->boolean('show_name'),
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->validated('password'));
        }

        $user->save();

        return back()->with('status', __('messages.profile_updated'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function usersIndex(Request $request): View
    {
        $query = User::query()->withCount('projects');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('nickname', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('projects_count')->get();

        return view('user.users.index', [
            'users' => $users,
            'search' => $request->input('search', ''),
        ]);
    }

    public function publicProfile(User $user): View
    {
        $projects = $user->projects()
            ->where('visibility', 'public')
            ->withCount('stars')
            ->latest()
            ->get();

        $starredProjects = $user->starredProjects()
            ->where('visibility', 'public')
            ->withCount('stars')
            ->latest('stars.created_at')
            ->get();

        return view('user.profile.public', [
            'profileUser' => $user,
            'projects' => $projects,
            'starredProjects' => $starredProjects,
        ]);
    }
}
