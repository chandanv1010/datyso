<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductDescription;
use App\Models\ProductImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $users = User::get();
        $items = Product::with('images', 'descriptions')->orderBy('del_flg', 'ASC')->get();

        $itemDescriptions = ProductDescription::with('product')->orderBy('id', 'DESC')->get();

        foreach ($items as $item) {
            foreach ($item->images as $image) {

                $image->image = url('storage/images/'. '/' .$image->image);
            }
        }

        return view('admin.product.list', ['items' => $items, 'users' => $users, 'itemDescriptions' => $itemDescriptions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();

        if ($categories->isEmpty()) {
            return back()->with('messageError', 'Bạn vui lòng tạo danh mục sản phẩm trước')->withInput();
        }

        $sortedCategories = [];
        $parentId = null;
    
        // Helper function to add children categories
        function addChildren($categories, $parentId, &$sortedCategories)
        {
            foreach ($categories as $category) {
                if ($category->parent_id == $parentId) {
                    $sortedCategories[] = $category;
                    addChildren($categories, $category->id, $sortedCategories);
                }
            }
        }
    
        // Build the sorted categories list
        addChildren($categories, $parentId, $sortedCategories);
        
        return view ('admin.product.form',['categories' => $sortedCategories, 'isUpdate' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        $category = Category::where([['id', $request['category_id']], ['status', 1]])->first();
        if (!$category) {
            return back()->with('messageError', config('message.data_not_found'))->withInput();
        }
        $attributeData = [];
        foreach ($request->attributeContent as $key => $attrContent) {
            if ($attrContent) {
                $attrName = ""; 
                array_push($attributeData, [$attrName, $attrContent]);
            }
        }

        try {
            DB::beginTransaction(); 

            $item = Product::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'tech_detail' => $request->tech_detail,
                'accessory_detail' => $request->accessory_detail,
                'voltage' => $request->voltage,
                'video' => $request->video,
                'slug' => $request->slug,
                'price' => convert_price($request->price),
                'price_discount' => convert_price($request->price_discount),
                
                'album' => $request->album,
            ]);

            ProductCategory::insert([
                'product_id' => $item->id,
                'category_id' => $request->category_id
            ]);

            $image = $request->file('image');
            if(isset($image)) {
                $nameImg = 'imgProd/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                ProductImage::insert([
                    'product_id' => $item->id,
                    'image' => $nameImg
                ]);
            }

            foreach ($attributeData as $key => $attrData) {
                $itemAttr = new ProductDescription([ 
                    'product_id' => $item->id,
                    'content' => $attrData[1],
                ]);

                $itemAttr->save();
            }
            DB::commit();
            $image->storeAs('images', $nameImg, 'public');
            return redirect()->route('admin.product.index')->with('messageSuccess', config('message.create_success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.product.create')->with('messageError', config('message.create_error'));
        }
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Product::with(['categories', 'images', 'descriptions'])
        ->where('id', $id)->first();

        if (!$item) {
            return redirect()->route('admin.product.index')->with('messageError', config('message.data_not_found'));
        }
    
        $productCategory = ProductCategory::where('product_id', $id)->first();
        if ($productCategory) {
            $item->category_id = $productCategory->category_id;
        }

        foreach ($item->images as $image) {
            $image->image = url('storage/images/'. '/' .$image->image);
        }
    
        $categories = Category::where('status', 1)->orderBy('name', 'asc')->get();
        if (count($categories) == 0) {
            return redirect()->route('admin.product.index')->with('messageError', config('message.data_inactive'));
        }

        return view('admin.product.form', ['item' => $item, 'categories' => $categories, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $category = Category::where([['id', $request['category_id']], ['status', 1]])->first();
        if (!$category) {
            return back()->with('messageError', config('message.data_not_found'))->withInput();
        }

        $attributeData = [];
        foreach ($request->attributeContent as $key => $attrContent) {
            if ($attrContent) {
                $attrName = "";
                array_push($attributeData, [$attrName, $attrContent]);
            }
        }

        try {
            DB::beginTransaction(); 
            $item = Product::findOrFail($id);       
            $item->user_id = $request->user_id;
            $item->name = $request->name;
            $item->description = $request->description;
            $item->status = $request->status;
            $item->tech_detail = $request->tech_detail;
            $item->accessory_detail = $request->accessory_detail;
            $item->voltage = $request->voltage;
            $item->video = $request->video;
            $item->slug = $request->slug;
            $item->price = convert_price($request->price);
            $item->price_discount = convert_price($request->price_discount);
            $item->album = $request->album;


            $item->save();

            ProductCategory::updateOrCreate(
                ['product_id' => $item->id],
                ['category_id' => $request->category_id]
            );
        
            $oldImage = ProductImage::where('product_id', $item->id)->first();
            $image = $request->file('image');
            if ($image) {

                $nameImg = 'imgProd/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                ProductImage::updateOrCreate(
                    ['product_id' => $item->id],
                    ['image' => $nameImg]
                );
            }
        
            $item->descriptions()->delete();
            foreach ($attributeData as $key => $attrData) {
                $itemAttr = new ProductDescription([
                    'product_id' => $item->id,
                    'content' => $attrData[1],
                ]);
                $itemAttr->save();
            }
        
            DB::commit();
        
            if ($image) {
                $image->storeAs('images', $nameImg, 'public');
        
                if ($oldImage) {
                    $path = url('storage/images/'. '/' .$oldImage);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }
        
            return redirect()->route('admin.product.index')->with('messageSuccess', config('message.update_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.product.edit', ['item' => $item->id])->with('messageError', config('message.update_error'));
        }  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::where('id', $id)->first();
        if (!$product) return redirect()->route('admin.prod.index')->with('messageError', config('message.data_not_found'));
    
        try {
            DB::beginTransaction();
            $product->del_flg = 2;
            $product->status = 2;
            $product->save();
            DB::commit();
            return redirect()->route('admin.product.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.product.index')->with('messageError', config('message.delete_error'));
        }
    }    

    public function postStatus($id) 
    {
        $item = Product::where('id', $id)->first();
        if (!$item) {
            return back()->with('messageError', config('message.data_not_found'));
        }
        try {
            DB::beginTransaction();

            $item->status = ($item->status == 1 ? 0: 1);
            $item->save();

            DB::commit();
            return redirect()->route('admin.product.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.product.index')->with('messageError',  config('message.status_error'));
        }
    }
}
