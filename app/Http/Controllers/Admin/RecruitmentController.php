<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\CreateRequest;
use App\Http\Requests\Recruitment\UpdateRequest;
use App\Models\Job;
use App\Models\Recruitment;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Recruitment::orderBy('id', 'DESC')->get();

        foreach ($items as $imageName) {
            $imageName->image = url('storage/images/'. '/' . $imageName->image);
        }

        return view('admin.recruitment.list', compact(['items']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.recruitment.form', ['isUpdate' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $image = $request->file('image');
            if (isset($image)) {
                $nameImg = 'recruitment/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                Recruitment::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'image' => $nameImg,
                    'slug' => $request->slug,
                    'status' =>  $request->status,
                    'user_id' => $request->user_id,
                    'type' => $request->type,
                    'location' => $request->location,
                ]);
            }

            DB::commit();
            $image->storeAs('images', $nameImg, 'public');
            return redirect()->route('admin.recruitment.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.recruitment.create')->with('messageError', config('message.create_error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Recruitment::where('id', $id)->first();
        if (!$item) return redirect()->route('admin.recruitment.index')->with('messageError', config('message.data_not_found'));

        $item->image = url('storage/images/'. '/' . $item->image);

        return view('admin.recruitment.form', ['item' => $item, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id) 
    {
        try {
            DB::beginTransaction();

            $item = Recruitment::findOrFail($id);
            if (!$item) return redirect()->route('admin.recruitment.index')->with('messageError', config('message.data_not_found'));

            $oldPath = $item->image;
            $data = $request->all();

            if ( $request->hasFile('image')) {
                $image = $request->file('image');
                $nameImg = 'recruitment/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                $data['image'] = $nameImg;
            }
                unset($data['imageCheck']);
                $item->fill($data)->save();
    
                DB::commit();
                
                if ($request->hasFile('image')) {
                    $image->storeAs('images', $nameImg, 'public');
    
                    // Delete image in firestorage
                    $path = url('storage/images/'. '/' . $oldPath);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

            return redirect()->route('admin.recruitment.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.recruitment.update', $id)->with('messageError', config('message.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Recruitment::where('id', $id)->first();
        if (!$item) return redirect()->route('admin.recruitment.index')->with('messageError', config('message.data_not_found'));

        try {
            $oldPath = $item->image;
            DB::beginTransaction();
            $item->delete();
            DB::commit();

            // Delete image in firestorage
            $path = url('storage/images/'. '/' . $oldPath);
            if (file_exists($path)) {
                unlink($path);
            }
            return redirect()->route('admin.recruitment.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.recruitment.update', ['id' => $item->id])->with('messageError', config('message.delete_failed'));
        }
    }

    public function postStatus($id)
    {
        $item = Recruitment::where('id', $id)->first();
        if (!$item) return back()->with('messageError', config('message.data_not_found'));
        try {
            DB::beginTransaction();
            $item->status = ($item->status == 1 ? 2 : 1);
            $item->save();
            DB::commit();
            return redirect()->route('admin.recruitment.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.recruitment.index')->with('messageError', config('message.status_error'));
        }
    }

    public function singleApply() {
        $items = UserProfile::orderBy('id', 'DESC')->get();
        if (!$items) return redirect()->route('admin.recruitment.index')->with('messageError', config('message.data_not_found'));
        return view('admin.recruitment.listApply', ['items' => $items, 'isUpdate' => true]);
    }

    public function showFileApply($id)
    {
        $profile = UserProfile::findOrFail($id);
        return compact('profile');
    }

    public function postStatusAppLy($id)
    {
        $item = UserProfile::where('id', $id)->first();
        if (!$item) return back()->with('messageError', config('message.data_not_found'));
        try {
            DB::beginTransaction();
            $item->status = ($item->status == 1 ? 2 : 1);
            $item->save();
            DB::commit();
            return redirect()->route('admin.recruitment.apply')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.recruitment.apply')->with('messageError', config('message.status_error'));
        }
    }
}
