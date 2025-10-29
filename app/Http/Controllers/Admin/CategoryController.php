<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();
        $categories =  Category::with('parent')->orderBy('name', 'DESC')->get();

        foreach ($categories as $imageName) {
            $imageName->image = url("storage/images/$imageName->image");
        }
        return view('admin.category.list', ['categories' => $categories, 'users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('parent_id', null)->get();
        return view('admin.category.form', ['categories' => $categories, 'isUpdate' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        if ($request->parent_id) {
            $parentCategory = Category::where([['id', $request->parent_id], ['status', 1]])->first();
            if (!$parentCategory) {
                return redirect()->route('admin.category.create')->with('messageError', 'Danh mục bạn chọn đã thuộc danh mục khác hoặc không tồn tại.');
            }
        }

        // dd($request->all());

        try {
            DB::beginTransaction();

            $image = $request->file('image');
            $banner = $request->file('banner');
            $nameImg = 'category/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
            $nameBanner = 'category/' . (string) Str::uuid() . "." . $banner->getClientOriginalExtension();
            $categoryStorage = [$nameImg, $nameBanner];
            Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' => $request->parent_id,
                'image' => $nameImg,
                'banner' => $nameBanner,
                'status' =>  $request->status,
                'user_id' => $request->user_id,
            ]);
            DB::commit();
            foreach ( $categoryStorage as $fileName) {
                $image->storeAs('images', $fileName, 'public');
            }
            return redirect()->route('admin.category.index')->with('messageSuccess', config('message.create_success'));
        } catch(Throwable $th) {
            DB::rollback();
            Log::error($th);
            return redirect()->route('admin.category.create')->with('messageError', config('message.create_failed'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        if (!$category) return redirect()->route('admin.category.index')->with('messageError', config('message.data_not_found'));
        $categories = [];
        if (count($category->child) >= 0) {
            $categories = Category::where([['id', '!=', $category->id], ['parent_id', null]])->get();
        }

        $category->image = url("storage/images/$category->image");
        if ($category->banner) {
            $category->banner = url("storage/images/$category->banner");
        }

        return view('admin.category.form', ['category' => $category, 'categories' => $categories, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        if ($request->parent_id) {
            $parentCategory = Category::where([['id', $request->parent_id], ['parent_id', null]])->first();
            if (!$parentCategory) return redirect()->route('admin.category.update', $id)->with('messageError', 'Danh mục bạn chọn đã thuộc danh mục khác hoặc không tồn tại.');
        }

        
        try {
            DB::beginTransaction();
            $category = Category::findOrFail($id);
            if (!$category) return redirect()->route('admin.category.index')->with('messageError', config('message.data_not_found'));
            
            if($request['status'] != $category->status) {
                if (!$category->parent_id) {
                    $categories = $category->child;
                    foreach ($categories as $item) {
                        if ($item['status'] == 1) {
                            return redirect()->route('admin.category.update', $id)->with('messageError', 'Hiện có danh mục đang hoạt động thuộc danh mục này');
                        }
                    }
                } else {
                    if ($category->parent->status == 2) return redirect()->route('admin.category.update', $id)->with('messageError', 'Hiện danh mục cha của danh mục này đang bị tạm khóa');
                }
            }

            $oldPath = $category->image;
            $oldBanner = $category->banner;
            $data = $request->all();
            if ($request->has('image')) {
                $image = $request->file('image');
                $nameImg = 'category/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                $data['image'] = $nameImg;
            }
            unset($data['imageCheck'], $data['bannerCheck']);
            if ($request->has('banner')) {
                $banner = $request->file('banner');
                $nameBanner = 'category/' . (string) Str::uuid() . "." . $banner->getClientOriginalExtension();
                $data['banner'] = $nameBanner;
            }
            $category->fill($data)->save();

            DB::commit();

            if ($request->has('image')) {
                $request->image->storeAs('images', $nameImg, 'public');

            // Delete image in firestorage
            unlink($oldPath);
            }

            if ($request->has('banner')) {
                $request->banner->storeAs('images', $nameBanner, 'public');

            // Delete image in firestorage
            $path = url('storage/images/'. '/' . $$oldBanner);
            if (file_exists($path)) {
                unlink($path);
            }
        }

            return redirect()->route('admin.category.index')->with('messageSuccess', config('message.update_success'));
        } catch(Throwable $th) {
            DB::rollback();
            Log::error($th);
            return redirect()->route('admin.category.update', $id)->with('messageError', config('message.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::where('id', $id)->first();
            if (!$category) return redirect()->route('admin.category.index')->with('messageError', config('message.data_not_found'));
            
            //check category does exist products
            if ($category->products->count() > 0 ) {
                return redirect()->route('admin.category.index')->with('error', 'Không thể thao tác: Hiện tại đang có sản phẩm thuộc danh mục này.');
            }
            
            if($category->child->count() > 0) {
                if($category->child()->count() > 0) return redirect()->route('admin.category.update', ['id' => $category->id])->with('messageError', 'Không thể thao tác: Hiện đang có danh mục thuộc danh mục này');
            }
            $oldPath = $category->image;
            $bannerOldPath = $category->banner;
            DB::beginTransaction();
            $category->delete();
            DB::commit();

             // Delete image in firestorage
            if (file_exists($oldPath))
                unlink($oldPath);
            if ($bannerOldPath != null) {
                if (file_exists($bannerOldPath)) unlink($bannerOldPath);
             }
            return redirect()->route('admin.category.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.category.update', ['id' => $category->id])->with('messageError', config('message.delete_error'));
        }
    }

    public function postStatus($id) 
    {
        $category = Category::where('id', $id)->first();
        if (!$category) return back()->with('messageError', config('message.data_not_found'));
        if (!$category->parent_id) {
            $categories = $category->child;
            foreach ($categories as $item) {
                if ($item['status'] == 1) {
                    return redirect()->route('admin.category.index')->with('messageError', 'Không thể thao tác: Hiện có danh mục đang hoạt động thuộc danh mục này');
                }
            }
        } else {
            if ($category->parent->status == 2) return redirect()->route('admin.category.index')->with('messageError', 'Không thể thao tác: Hiện danh mục cha của danh mục này đang bị tạm khóa');
        }
        try {
            DB::beginTransaction();
            $category->status = ($category->status == 1 ? 2: 1);
            $category->save();
            DB::commit();
            return redirect()->route('admin.category.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.category.index')->with('messageError', config('message.status_error'));
        }
    }
}
