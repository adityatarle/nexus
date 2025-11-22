<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isDealer()) {
                return redirect()->intended(route('dealer.dashboard'));
            } else {
                return redirect()->intended(route('agriculture.home'));
            }
        }

        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:customer,dealer',
            'phone' => 'required|string|max:20',
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
            'viewable_password' => $request->password, // Store plain text for admin viewing
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        Auth::login($user);

        // Create welcome notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'welcome',
            'title' => 'Welcome to Nexus Agriculture!',
            'message' => 'Thank you for registering with us. You can now browse our agricultural products.',
        ]);

        // Redirect based on role
        if ($user->isDealer()) {
            return redirect()->route('dealer.registration')->with('success', 'Account created successfully! Please complete your dealer registration.');
        }

        return redirect()->route('agriculture.home')->with('success', 'Account created successfully!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('agriculture.home')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show dealer login form
     */
    public function showDealerLogin()
    {
        return view('auth.dealer-login');
    }

    /**
     * Handle dealer login
     */
    public function dealerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            if (!$user->isDealer()) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'This account is not registered as a dealer.'])
                    ->withInput($request->except('password'));
            }

            if (!$user->isApprovedDealer()) {
                return redirect()->route('dealer.pending')->with('info', 'Your dealer registration is pending approval.');
            }

            return redirect()->intended(route('dealer.dashboard'));
        }

        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    /**
     * Show customer login form
     */
    public function showCustomerLogin()
    {
        return view('auth.customer-login');
    }

    /**
     * Handle customer login
     */
    public function customerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            if (!$user->isCustomer()) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'This account is not registered as a customer.'])
                    ->withInput($request->except('password'));
            }

            return redirect()->intended(route('agriculture.home'));
        }

        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput($request->except('password'));
    }
}