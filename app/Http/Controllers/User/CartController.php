<?php  
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class CartController extends Controller {

    public function index(){
        $carts = Cart::content();
        return view('cart.index', compact('carts')); 
    }

    public function success($id){
        $order = Order::with('products')->find($id);
        return view('cart.success', compact('order')); 
    }


    public function checkout(CheckoutRequest $request){
        try {
            DB::beginTransaction();
            $order = Order::create([
                'fullname' => $request->fullname,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,

                'subtotal' => Cart::content()->sum(fn($item) => $item->price * $item->qty),
                'discount' => 0,
                'shipping_fee' => 0,
                'total' => Cart::content()->sum(fn($item) => $item->price * $item->qty),
                'status' => 'pending',
                'payment_method' => 'COD',
                'payment_status' => 'unpaid',
            ]);

            foreach (Cart::content() as $item) {
                $order->products()->attach($item->id, [
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->qty,
                    'product_name' => $item->name,
                ]);
            }

            DB::commit();
            Cart::destroy();
            return redirect()->route('cart.success', ['id' => $order->id])->with('success', 'Đặt hàng thành công');


        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);
            return back()->with('error', 'Có vấn đề xảy ra trong quá trình đặt hàng, Hãy thử lại sau');
        }
    }
    

}