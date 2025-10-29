<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())
            ->with('product')
            ->latest()
            ->get();

        return view('agriculture.wishlist.index', compact('items'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:agriculture_products,id',
        ]);

        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'agriculture_product_id' => $request->product_id,
        ]);

        return back()->with('success', 'Added to wishlist');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:agriculture_products,id',
        ]);

        Wishlist::where('user_id', Auth::id())
            ->where('agriculture_product_id', $request->product_id)
            ->delete();

        return back()->with('success', 'Removed from wishlist');
    }

    public function clear()
    {
        Wishlist::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Wishlist cleared');
    }
}


