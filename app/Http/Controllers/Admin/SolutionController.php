<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Solution\CreateRequest;
use App\Http\Requests\Solution\UpdateRequest;
use App\Models\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solutions = Solution::orderBy('id', 'DESC')->get();

        foreach ($solutions as $imageName) {
            $imageName->image = url('storage/images/'. '/' . $imageName->image);
        }

        return view('admin.solution.list', compact(['solutions']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.solution.form', ['isUpdate' => false]);
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
                $nameImg = 'solution/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                Solution::create([
                    'name' => $request->name,
                    'content' => $request->content,
                    'image' => $nameImg,
                    'slug' => $request->slug,
                    'status' =>  $request->status,
                    'user_id' => $request->user_id,
                ]);
            }

            DB::commit();
            $image->storeAs('images', $nameImg, 'public');
            return redirect()->route('admin.solution.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.solution.create')->with('messageError', config('message.create_error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $solution = Solution::where('id', $id)->first();
        if (!$solution) return redirect()->route('admin.solution.index')->with('messageError', config('message.data_not_found'));

        $solution->image = url('storage/images/'. '/' . $solution->image);

        return view('admin.solution.form', ['solution' => $solution, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id) 
    {
        try {
            DB::beginTransaction();

            $solution = Solution::findOrFail($id);
            if (!$solution) return redirect()->route('admin.solution.index')->with('messageError', config('message.data_not_found'));

            $oldPath = $solution->image;
            $data = $request->all();

            if ( $request->hasFile('image')) {
                $image = $request->file('image');
                $nameImg = 'solution/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                $data['image'] = $nameImg;
            }
                unset($data['imageCheck']);
                $solution->fill($data)->save();
    
                DB::commit();
                
                if ($request->hasFile('image')) {
                    $image->storeAs('images', $nameImg, 'public');
    
                    // Delete image in firestorage
                    $path = url('storage/images/'. '/' . $oldPath);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

            return redirect()->route('admin.solution.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.solution.update', $id)->with('messageError', config('message.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $solution = Solution::where('id', $id)->first();
        if (!$solution) return redirect()->route('admin.solution.index')->with('messageError', config('message.data_not_found'));

        try {
            $oldPath = $solution->image;
            DB::beginTransaction();
            $solution->delete();
            DB::commit();

            // Delete image in firestorage
            $path = url('storage/images/'. '/' . $oldPath);
            if (file_exists($path)) {
                unlink($path);
            }
            return redirect()->route('admin.solution.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.solution.update', ['id' => $solution->id])->with('messageError', config('message.delete_failed'));
        }
    }

    public function postStatus($id)
    {
        $solution = Solution::where('id', $id)->first();
        if (!$solution) return back()->with('messageError', config('message.data_not_found'));
        try {
            DB::beginTransaction();
            $solution->status = ($solution->status == 1 ? 2 : 1);
            $solution->save();
            DB::commit();
            return redirect()->route('admin.solution.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.solution.index')->with('messageError', config('message.status_error'));
        }
    }
}
