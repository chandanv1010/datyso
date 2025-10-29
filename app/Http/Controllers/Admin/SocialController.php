<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\CreateRequest;
use App\Http\Requests\Social\UpdateRequest;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SocialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $socials = Social::orderBy('id', 'DESC')->get();
        return view('admin.social.list', ['socials' => $socials]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.social.form', ['isUpdate' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $social = Social::create([
                'name' => $request->name,
                'url' => $request->url,
                'image' => $request->image,
                'status' => $request->status,
            ]);
            
            DB::commit();

            return redirect()->route('admin.social.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.social.create')->with('messageError', config('message.create_error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $social = Social::where('id', $id)->first();
        if (!$social) {
            return redirect()->route('admin.social.index')->with('messageError', config('message.update_error'));
        }

        return view('admin.social.form', ['social' => $social, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $social = Social::where('id', $id)->first();
        if (!$social) {
            return redirect()->route('admin.social.index')->with('messageError', config('message.update_error'));
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            $social->fill($data)->save();

            DB::commit();

            return redirect()->route('admin.social.index')->with('messageSuccess', config('message.update_success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.social.index')->with('messageError', config('message.update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $social = Social::where('id', $id)->first();
        if (!$social) {
            return redirect()->route('admin.social.index')->with('messageError', config('message.data_not_found'));
        }

        try {
            DB::beginTransaction();
            $social->delete();
            DB::commit();

            return redirect()->route('admin.social.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.social.update', ['id' => $social->id])->with('messageError', config('message.delete_error'));
        }
    }

    public function postStatus($id)
    {
        $social = Social::where('id', $id)->first();
        if (!$social) {
            return back()->with('messageError', config('message.data_not_found'));
        }

        try {
            DB::beginTransaction();

            $social->status = ($social->status == 1 ? 0 : 1);
            $social->save();

            DB::commit();
            return redirect()->route('admin.social.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.social.index')->with('messageError', config('message.status_error'));
        }
    }
}
