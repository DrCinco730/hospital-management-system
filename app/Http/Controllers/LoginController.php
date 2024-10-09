<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use App\Models\UserAdmin;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return View
     */
    public function showLoginForm(): View
    {
        return view('auth.login'); // Blade template for the login form
    }

    /**
     * Handle the login request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Please enter your username.',
            'password.required' => 'Please enter your password.',
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare user credentials
        $credentials = $request->only('username', 'password');

        // Verify if the user exists
        $view_user = UserAdmin::where('username', $credentials['username'])->first();

        if (!$view_user) {
            return redirect()->back()
                ->withErrors(['loginError' => 'Invalid username. Please try again.'])
                ->withInput();
        }

        // Verify if the password matches
        if (!Hash::check($credentials['password'], $view_user->password)) {
            return redirect()->back()
                ->withErrors(['loginError' => 'Invalid password. Please try again.'])
                ->withInput();
        }

        // Determine user type and authenticate
        if ($view_user->type === 'admin') {
            $user = Admin::find($view_user->id);
            Auth::guard('admin')->login($user);
            $redirectPath = 'admin';
            $welcomeMessage = 'Welcome Admin!';
        } else {
            $user = User::find($view_user->id);
            Auth::login($user, $request->filled('remember'));
            $redirectPath = 'home';
            $welcomeMessage = 'Welcome User!';
        }

        // Regenerate session for security
        $request->session()->regenerate();

        // Redirect to the intended page with a success message
        return redirect()->intended($redirectPath)->with('success', $welcomeMessage);
    }

    /**
     * Handle the logout request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        // Log the user out
        Auth::logout();

        // Invalidate and regenerate session to prevent reuse
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the login page with a success message
        return redirect('/login')->with('success', 'You have successfully logged out.');
    }
}
