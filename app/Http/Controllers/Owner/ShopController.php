<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use Closure;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        // Shop編集ページで、他のShopのURLにアクセスした際に見れてしまうのを防ぐため、middlewareでログインしている店舗情報か判別する
        $this->middleware(function (Request $request, Closure $next) {
            // dd($request->route()->parameter('shop')); // 文字列
            // dd(Auth::id()); // 数字
            // dd($next($request));

            $id = $request->route()->parameter('shop');
            if(!is_null($id)) {
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId; // 文字列で取得するので、数字に変換
                $ownerId = Auth::id();
                if($shopId !== $ownerId) {
                    abort((404)); // 404ページへ
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        // $ownerId = Auth::id();
        $shops = Shop::where('owner_id', Auth::id())->get();

        return view('owner.shops.index', compact('shops'));
    }

    public function edit(string $id)
    {
        dd(Shop::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {}
}
