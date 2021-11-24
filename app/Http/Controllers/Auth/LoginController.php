<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use function redirect;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {

        $user->update([
            'etat_user' => 1
        ]);
        if ($user->role == 'Concepteur' or $user->role == "Administrateur") {
            return redirect('/home');
        }
        if($user->role == "Caissier") {
            return redirect()->route('vente.point-caisse');
        }
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    protected function credentials(Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['confirmation_token' => null],
            ['statut_compte' => 1],
            ['deleted_at' => null]
        );
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp(),
            'etat_user' => 0
        ]);

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }
}
