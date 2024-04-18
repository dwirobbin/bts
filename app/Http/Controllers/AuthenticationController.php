<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\{Role, User};
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Mail};
use App\Providers\RouteServiceProvider;
use App\Http\Requests\AuthenticationRequest;
use Cviebrock\EloquentSluggable\Services\SlugService;

class AuthenticationController extends Controller
{
    public function indexLogin()
    {
        return view('pages.authentications.login', [
            'title' => 'Login',
            'show_sidebar' => false,
        ]);
    }

    public function loginProcess(AuthenticationRequest $request)
    {
        $credential = [
            'email'     => $request['email'],
            'password'  => $request['password'],
        ];

        if (auth()->attempt($credential)) {
            if (auth()->user()->is_active) {
                $request->session()->regenerate();
                return redirect()->intended(RouteServiceProvider::HOME);
            }

            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->back()->withError('Gagal Login.');
        }

        return redirect()->back()->withError('Gagal Login.');
    }

    public function indexRegister()
    {
        return view('pages.authentications.register', [
            'title' => 'Register',
            'show_sidebar' => false,
        ]);
    }

    public function registerProcess(AuthenticationRequest $request)
    {
        $slug = SlugService::createSlug(User::class, 'slug', Str::title($request['name']));

        $role = Role::query()->whereName('customer')->first();

        $user = new User();
        $user->name = str($request['name'])->title();
        $user->slug = $slug;
        $user->email = $request['email'];
        $user->password = $request['password'];
        $user->role()->associate($role);
        $user->save();

        return redirect()->to('/auth/login')->withSuccess('Berhasil daftar, Silahkan login.');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }

    public function showForgotPassword()
    {
        return view('pages.authentications.forgot-password', [
            'title' => 'Lupa Password',
            'show_sidebar'  => false,
        ]);
    }

    public function submitForgetPasswordForm(AuthenticationRequest $request)
    {
        $token = base64_encode(Str::random(64));

        $oldToken = DB::table('password_reset_tokens')->where(['email' => $request['email']])->first();

        if ($oldToken) {
            DB::table('password_reset_tokens')
                ->where(['email' => $request['email']])
                ->update([
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
        } else {
            DB::table('password_reset_tokens')->insert([
                'email' => $request['email'],
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        }

        Mail::send('pages.authentications.email-templates.forgot-password', [
            'token' => $token,
            'email' => $request->email,
        ], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        session()->flash('success', 'kami telah mengirim tautan reset password Anda melalui email');
        return redirect()->to('auth/forgot-password');
    }

    public function showResetPasswordForm($token = null)
    {
        $checkToken = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if ($checkToken) {
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s', $checkToken->created_at)
                ->diffInMinutes(Carbon::now());

            if ($diffMins > 15) {
                session()->flash('error', 'Token expired!, request another reset password link');
                return redirect()->route('forget_password', ['token' => $token]);
            } else {
                return view('pages.authentications.reset-password', [
                    'title' => 'Reset Password',
                    'show_sidebar'  => false,
                    'token' => $token,
                ]);
            }
        } else {
            session()->flash('error', 'Invalid token!, request another reset password link');
            return redirect()->route('forget_password', ['token' => $token]);
        }
    }

    public function submitResetPasswordForm(AuthenticationRequest $request)
    {
        $token = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        $user = User::where('email', $token->email)->first();

        User::where('email', $user->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where([
            'email' => $user->email,
            'token' => $request->token
        ])->delete();

        Mail::send('pages.authentications.email-templates.reset-password', [
            'email' => $user->email,
            'new_password' => $request->password,
        ], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Password diubah');
        });

        return redirect()->to('auth/login')->with('success', 'Password reset success');
    }
}
