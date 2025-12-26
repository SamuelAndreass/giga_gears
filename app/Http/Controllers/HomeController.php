<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Seminar;
class HomeController extends Controller
{
   public function index()
    {
        $categories = Category::all(); 
        $products = Product::take(6)->get();
        $seminars = Seminar::where('status', '!=', 'cancelled')->orderBy('start_date', 'asc')->take(5)->get();

        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return view('customer.home', compact('categories', 'products', 'cartCount', 'seminars'));
    }
}