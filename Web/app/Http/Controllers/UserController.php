<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return View('Auth.login');
    }


    public function login(Request $request)
    {

        try {
            $user = User::query()
                ->where('email', $request->get('email'))
//                ->where('password', $request->get('password'))
                ->firstOrFail();
            if(!Hash::check($request->get('password'), $user->password)){
                throw new Exception('404 Not Found');
            }
            session()->put('level', $user->level);
            session()->put('avatar', $user->avatar);
            session()->put('name', $user->name);

            return redirect()->route('course.index');
        } catch (\Exception $e) {
            return redirect()->route('Login.index');
        }
    }


    public function logout()
    {
        session()->forget('level');
        session()->forget('name');

        return redirect()->route('Login.index');
    }

    public function resign()
    {
        return View('Auth.resigner');
    }

    public function account(Request $request)
    {
        $path = Storage::disk('public')->putFile('avatars', $request->file('avatar'));
        User::query()->create([
            'name' => $request->get('name'),
            'avatar' => $path,
            'level' => 0,
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        return redirect()->route('Login.index');
    }

}
