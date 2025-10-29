<?php  
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller {

    public function __construct()
    {
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $users = User::get();
        $orders = Order::with('products')->orderBy('created_at', 'ASC')->get();


        return view('admin.order.list', compact('orders', 'users'));
    }


}