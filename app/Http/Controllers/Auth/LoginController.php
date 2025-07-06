<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect user based on their role
     *
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('HR Manager')) {
            return redirect()->route('hr.dashboard');
        } elseif ($user->hasRole('Department Head')) {
            return redirect()->route('dept.dashboard');
        } elseif ($user->hasRole('Finance Officer')) {
            return redirect()->route('finance.dashboard');
        } else {
            return redirect()->route('employees.dashboard');
        }
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}