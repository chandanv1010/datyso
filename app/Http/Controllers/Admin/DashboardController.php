<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Product;
use App\Models\Views;
use App\Models\ViewsPost;
use App\Models\ViewsProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class DashboardController extends Controller
{
    public function getLogin()
    {
        if (Auth::check()) return redirect()->route('admin.dashboard.index');
        return view('admin.dashboard.login');
    }

    public function postLogin(LoginRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.login')->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        
        if (Auth::guard('admin')->attempt(['username' => $validatedData['username'], 'password' => $validatedData['password']])) {
            if(Auth::guard('admin')->user()->status == 1 ) {
                return redirect()->route('admin.dashboard.index');
            } else {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('messageError', 'Bạn không có quyền truy cập');;
            }   
        }
        return redirect()->route('admin.login')->with('messageError',  config('message.login_failed'))->withInput();
    }

    public function getLogOut()
    {
        if ( Auth::guard('admin')) {
            return redirect()->route('admin.login');
        }
        return redirect()->route('admin.login');
    }

    public function getData() {
        return view('admin.dashboard.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $TotalViewsCount = Views::count();
        
        $newUserViewsCount = Views::where('times', 1)->count();
        $oldUserViewsCount = Views::where('times', '!=' ,1)->count();
        $newVisitorRate = 0;
        if ($oldUserViewsCount > 0) {
            $newVisitorRate = round(($newUserViewsCount/$oldUserViewsCount)*100, 2);
        }

//----------------------------------------------------------------

        $top10ProductViews = ViewsProduct::select('product_id', DB::raw('SUM(times) as total_views'))
            ->groupBy('product_id')
            ->orderByDesc('total_views')
            ->limit(10)
            ->get()
        ->toArray();

        $top10ProductViewsLabel = array_map(function($item) {
            $productData = Product::find($item['product_id']);
            return $productData->name;
        }, $top10ProductViews);


        $top10ProductViewsData = array_map(function($item) {
            return $item['total_views'];
        }, $top10ProductViews);
//----------------------------------------------------------------

        $top5PostViews = ViewsPost::select('post_id',  DB::raw('SUM(times) as total_views'))
            ->groupBy('post_id')
            ->orderByDesc('total_views')
            ->limit(5)
            ->get()
        ->toArray();

        $top5PostViewsLabel = array_map(function($item) {
            $postData = Post::find($item['post_id']);
            return $postData['name'];
        }, $top5PostViews);


        $top5PostViewsData = array_map(function($item) {
            return $item['total_views'];
        }, $top5PostViews);
//----------------------------------------------------------------

        $contactBeenPressed = Contact::where('product_id', '!=' , null)->count();
        $totalViewsProductTimes = ViewsProduct::sum('times');
        $percentageContactBeenPressed = round(($contactBeenPressed/$totalViewsProductTimes)*100, 2);
//----------------------------------------------------------------

        $data = [
            'TotalViewsCount' => (int)$TotalViewsCount,
            'percentageContactBeenPressed' => $percentageContactBeenPressed,
            'newVisitorRate' => $newVisitorRate,
            'top10ProductViewsLabel' => $top10ProductViewsLabel,
            'top10ProductViewsData' => $top10ProductViewsData,
            'top5PostViewsLabel' => $top5PostViewsLabel,
            'top5PostViewsData' => $top5PostViewsData,
        ];
        
        return view('admin.dashboard.index', ['data' => $data]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
