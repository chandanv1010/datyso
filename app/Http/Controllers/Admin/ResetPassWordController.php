<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\VeryFileEmailRequest;
use App\Mail\UserMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ResetPassWordController extends Controller
{
    public function getForgotPassword()
    {
        return view('admin.dashboard.forgot-password');
    }

    public function postForgotPassword(VeryFileEmailRequest $request) 
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->route('admin.resetPassword.create');
            }

            $user->email_verified_at = Carbon::now();

            $user->forgot_url = Str::random(64);
            $user->save();
    
            Mail::to($user->email)->send(new UserMail($user->forgot_url, $user->username));
    
            DB::commit();
            
            return back()->with('messageSuccess', config('message.send_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
           return redirect()->route('admin.resetPassword.create')->with('messageError', config('message.send_error'));
        }
    }

    public function showResetPasswordForm($forgot_url) 
    {
        return view('admin.resetPasswords.check-pass-word', ['forgot_url' => $forgot_url]);
    }

    public function submitResetPasswordForm(UpdatePasswordRequest $request) 
    { 
        try {
            DB::beginTransaction();
            $pass_token = Hash::make($request->password);

            DB::table('m_user')->update([
                'password' => $pass_token
            ]);

            DB::commit();
            return redirect()->route('admin.login')->with('messageSuccess', config('message.send_success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
           return redirect()->route('admin.resetPassword.getCreatePass')->with('messageError', config('message.send_error'));
        }
    }
}
