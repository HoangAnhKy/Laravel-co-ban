<?php

namespace App\Http\Controllers;

use App\Events\SendMail_even;
use App\Listeners\Listener_Sendmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    public function login()
    {
        return View('User.login');
    }

    public function resigner()
    {
        return View('User.resigner');
    }

    public function checkout(Request $request)
    {
        try {
            $user = User::query()->where('email', $request->get('email'))->firstOrFail();

            SendMail_even::dispatch($user);
            if (!Hash::check($request->get('password'), $user->password)) {
                throw new \Exception('404 Not Found');
            }
            session()->put('lever', $user->lever);
            session()->put('name', $user->name);
            return redirect()->route('Courses.index');
        } catch (\Exception $e) {
            return redirect()->route('login');
        }
    }

    public function callback($provider)
    {
        $data = Socialite::driver($provider)->user();
        $user = user::query()->firstOrCreate([
            'email' => $data->email,
        ], [
            'name' => $data->name,
        ]);
//        SendMail_even::dispatch($user);
        Auth::login($user);

        return redirect()->route('resigner');
    }
}
