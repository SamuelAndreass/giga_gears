<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
class HomeController extends Controller
{
   public function index()
    {
        $categories = Category::all(); 
        $products = Product::where('stock', '>', 0)->take(6)->get(); 
        $bundles = Product::with('bundleItems.product')->where('type', 'bundle')->where('stock', '>', 0)->take(3)->get();


        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return view('customer.home', compact('categories', 'products', 'cartCount', 'bundles'));
    }
}