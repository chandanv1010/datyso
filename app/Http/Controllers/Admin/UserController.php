<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users =  User::where('role', 2)->orderBy('username', 'asc')->get();
        return view('admin.user.list', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.form', ['isUpdate' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        try {
            DB::beginTransaction();
            User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' =>  Hash::make($request->password),
                'phone' => $request->phone,
                'status' => $request->status,
            ]);
            DB::commit();
            return redirect()->route('admin.user.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.user.create')->with('messageError', config('message.create_failed'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::where('id', $id)->where('role', 2)->first();
        if (!$user) return redirect()->route('admin.user.index')->with('messageError', config('message.update_failed'));
        return view('admin.user.form', ['user' => $user, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return redirect()->route('admin.user.index')->with('messageError', config('message.update_failed'));
        }

        if($request->password) {
            $data = [
                'username' => $request->username,
                'email' => $request->email,
                'password' =>  Hash::make($request->password),
                'phone' => $request->phone,
                'status' => $request->status,
            ];
        } else {
            $data = [
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status
            ];
        }

        try {
            DB::beginTransaction();
            $user->update($data);
            DB::commit();
            
            return redirect()->route('admin.user.index')->with('messageSuccess', config('message.update_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.user.update', ['id' => $id])->with('messageError', config('message.update_failed'));
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where([['id', $id], ['role', 2]])->first();
        if (!$user) return redirect()->route('admin.user.index')->with('messageError', config('message.data_not_found'));
        try {
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.user.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.user.update', ['id' => $user->id])->with('messageError', config('message.delete_error'));
        }
    }

    public function postStatus($id)
    {
        $user = User::where([['id', $id], ['role', 2]])->first();
        if (!$user) return back()->with('messageError', config('message.data_not_found'));
        try {
            DB::beginTransaction();
            $user->status = ($user->status == 1 ? 2 : 1);
            $user->save();
            DB::commit();
            return redirect()->route('admin.user.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.user.index')->with('messageError', config('message.status_error'));
        }
    }
}
