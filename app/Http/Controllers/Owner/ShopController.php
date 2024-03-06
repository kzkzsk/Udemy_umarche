<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use Closure;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;


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

        // phpinfo();
        // $ownerId = Auth::id();
        $shops = Shop::where('owner_id', Auth::id())->get();

        return view('owner.shops.index', compact('shops'));
    }

    public function edit(string $id)
    {
        $shop = Shop::findOrFail($id);
        // dd(Shop::findOrFail($id));

        return view('owner.shops.edit', compact('shop'));
    }

    public function update(UploadImageRequest $request, string $id)
    {
        $imageFile = $request->image;
        if( !is_null($imageFile) && $imageFile->isValid() ) {
            // dd($imageFile);
            // Storage::putFile('public/shops/', $imageFile); // リサイズなしの場合

            // interventionImageを使用したリサイズ処理（動作しないため不使用）
            // $fileName = uniqid((rand().'_'));
            // $extension = $imageFile->extension();
            // $fileNameToStore = $fileName . '.' . $extension;
            // $resizedImage = Image::create(1920, 1080)->encode();

            // dd($imageFile, $resizedImage);
            // Storage::put('public/shops' . $fileNameToStore, $resizedImage);

            $file = ImageService::upload($imageFile, 'shops');
        }

        return redirect()->route('owner.shops.index');
    }
}
