<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\PostRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'DESC')->get();

        foreach ($posts as $imageName) {
            $imageName->image = url('storage/images/'. '/' . $imageName->image);
        }

        return view('admin.post.list', compact(['posts']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.post.form', ['isUpdate' => false]);
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
                $nameImg = 'post/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                Post::create([
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
            return redirect()->route('admin.post.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.post.create')->with('messageError', config('message.create_error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::where('id', $id)->first();
        if (!$post) return redirect()->route('admin.post.index')->with('messageError', config('message.data_not_found'));

        $post->image = url('storage/images/'. '/' . $post->image);

        return view('admin.post.form', ['post' => $post, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id) 
    {
        try {
            DB::beginTransaction();

            $post = Post::findOrFail($id);
            if (!$post) return redirect()->route('admin.post.index')->with('messageError', config('message.data_not_found'));

            $oldPath = $post->image;
            $data = $request->all();

            if ( $request->hasFile('image')) {
                $image = $request->file('image');
                $nameImg = 'post/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                $data['image'] = $nameImg;
            }
                unset($data['imageCheck']);
                $post->fill($data)->save();
    
                DB::commit();
                
                if ($request->hasFile('image')) {
                    $image->storeAs('images', $nameImg, 'public');
    
                    // Delete image in firestorage
                    $path = url('storage/images/'. '/' . $oldPath);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

            return redirect()->route('admin.post.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.post.update', $id)->with('messageError', config('message.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::where('id', $id)->first();
        if (!$post) return redirect()->route('admin.post.index')->with('messageError', config('message.data_not_found'));

        try {
            $oldPath = $post->image;
            DB::beginTransaction();
            $post->delete();
            DB::commit();

            // Delete image in firestorage
            $path = url('storage/images/'. '/' . $oldPath);
            if (file_exists($path)) {
                unlink($path);
            }
            return redirect()->route('admin.post.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.post.update', ['id' => $post->id])->with('messageError', config('message.delete_failed'));
        }
    }

    public function postStatus($id)
    {
        $post = Post::where('id', $id)->first();
        if (!$post) return back()->with('messageError', config('message.data_not_found'));
        try {
            DB::beginTransaction();
            $post->status = ($post->status == 1 ? 2 : 1);
            $post->save();
            DB::commit();
            return redirect()->route('admin.post.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.post.index')->with('messageError', config('message.status_error'));
        }
    }
}
