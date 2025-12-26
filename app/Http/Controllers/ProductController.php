<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View {
        $query = Product::with('category');
        if ($request->has('search') && !empty($request->search)) { $query->search($request->search); }
        if ($request->has('category') && $request->category != 'all') { $query->where('category_id', $request->category); }
        if ($request->has('min_price') && is_numeric($request->min_price)) { $query->where('discount_price', '>=', $request->min_price); }
        if ($request->has('max_price') && is_numeric($request->max_price)) { $query->where('discount_price', '<=', $request->max_price); }
        if ($request->has('brand') && $request->brand != 'all') { $query->where('brand', $request->brand); }
        if ($request->has('featured')) { $query->where('is_featured', true); }
        if ($request->has('in_stock')) { $query->where('stock', '>', 0); }
        $sort = $request->get('sort', 'created_at'); $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order); $products = $query->paginate(12);
        $categories = Category::all(); $brands = Product::select('brand')->distinct()->pluck('brand');
        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show($id): View {
        $product = Product::with(['category', 'reviews'])->findOrFail($id);
        if ($product->reviews->count() > 0) {
            $product->rating = $product->reviews->avg('rating'); $product->review_count = $product->reviews->count(); $product->save();
        }
        $relatedProducts = Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->with('category')->limit(4)->get();
        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function featured(): View {
        $products = Product::featured()->with('category')->orderBy('created_at', 'desc')->limit(8)->get();
        return view('products.featured', compact('products'));
    }

    public function search(Request $request): View {
        $search = $request->get('q', ''); $products = [];
        if (!empty($search)) { $products = Product::search($search)->with('category')->orderBy('created_at', 'desc')->paginate(12); }
        return view('products.search', compact('products', 'search'));
    }

    public function byCategory($categoryId): View {
        $category = Category::findOrFail($categoryId);
        $products = Product::where('category_id', $categoryId)->with('category')->orderBy('created_at', 'desc')->paginate(12);
        return view('products.category', compact('products', 'category'));
    }

    public function apiSearch(Request $request) {
        $search = $request->get('q', ''); $products = [];
        if (!empty($search)) { $products = Product::search($search)->with('category')->limit(10)->get(['id', 'name', 'discount_price', 'images', 'brand']); }
        return response()->json($products);
    }

    public function addReview(Request $request, $id) {
        $validated = $request->validate(['user_name' => 'required|string|max:255', 'rating' => 'required|integer|between:1,5', 'comment' => 'required|string|min:10']);
        ProductReview::create(['product_id' => $id, 'user_name' => $validated['user_name'], 'rating' => $validated['rating'], 'comment' => $validated['comment'], 'is_verified' => true]);
        $product = Product::with('reviews')->find($id); $product->rating = $product->reviews->avg('rating'); $product->review_count = $product->reviews->count(); $product->save();
        return redirect()->back()->with('success', 'Review added successfully!');
    }
}