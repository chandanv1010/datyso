<?php  
namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Google\Cloud\Storage\Connection\Rest;

class CartController extends Controller {

    private $product;

    private function setProduct(int $productId): self{
        $this->product = Product::with(['images'])->find($productId);
        return $this;
    }

    public function create(Request $request){
        $this->setProduct($request->id);
        $image = $this->product->images->first()->image;

        $price = $this->product->price ?? 0;
        $price_discount = $this->product->price_discount ?? 0;

        $finalPrice = $price_discount > 0 ? $price_discount : $price;

        Cart::add($this->product->id, $this->product->name, 1, $finalPrice, 0, ['image' => $image, 'price_original' => (int)$price, 'price_discount' => (int)$price_discount]);
        return response()->json([
            'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'status' => true 
        ]);
    }

    public function update(Request $request){
        $payload = $request->input();
        Cart::update($payload['rowId'], $payload['qty']);
        $cartTotal = Cart::content()->sum(function ($item) {
            return $item->qty * $item->price;
        });
        return response()->json([
            'message' => 'Cập nhật số lượng thành công',
            'status' => true,
            'response' => [
                'cartTotal' => $cartTotal,
            ] 
        ]);
    }

    public function delete(Request $request){
        $payload = $request->input();
        Cart::remove($payload['rowId']);
        $cartTotal = Cart::content()->sum(function ($item) {
            return $item->qty * $item->price;
        });
        return response()->json([
            'message' => 'Xóa thành công sản phẩm khỏi giỏ hàng',
            'status' => true,
            'response' => [
                'cartTotal' => $cartTotal,
            ] 
        ]);
    }


}