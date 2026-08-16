<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function username()
    {
        return 'identity';
    }

    /**
     * Where to redirect users after logout.
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('login');
    }

    protected function credentials(Request $request)
    {
        $identity = $request->input($this->username());

        // Check if identity is email or username
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $identity,
            'password' => $request->input('password'),
        ];
    }
}
