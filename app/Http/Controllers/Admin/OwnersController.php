<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner; // Eloquant エロくアント
use Illuminate\Support\Facades\DB; // QueryBuilder クエリビルダー
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Shop;
use Throwable;
use Illuminate\Support\Facades\Log;

class OwnersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $date_now = Carbon::now();
        // $date_parse = Carbon::parse((now()));
        // echo $date_now->year;
        // echo $date_parse;


        // $e_all = Owner::all();
        // $q_get = DB::table('owners')->select('name', 'created_at')->get();
        // $q_first = DB::table('owners')->select('name')->first();

        // $c_test = collect([
        //     'name' => 'てすと'
        // ]);

        // var_dump($q_first);

        // dd($e_all, $q_get, $q_first, $c_test);

        // return view('admin.owners.index',
        // compact('e_all', 'q_get'));


        $owners = Owner::select('id', 'name', 'email', 'created_at')
        ->paginate(3);

        return view('admin.owners.index',
        compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 上記 function storeの引数にフォームの内容が格納されている
        // $request->name; フォームに入力した名前が取得できる

        // バリデーション
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Owner::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try{
            DB::transaction(function() use($request) {
                $owner = Owner::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                Shop::create([
                    'owner_id' => $owner->id,
                    'name' => '店名を入力してください',
                    'information' => '',
                    'filename' => '',
                    'is_selling' => true
                ]);
            }, 2);
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナー登録を実施しました。',
            'status' => 'info'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $owner = Owner::findOrFail($id);
        // dd($owner);
        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->password = Hash::make($request->password);
        $owner->save();

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナー情報を更新しました。',
            'status' => 'info'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // dd('削除処理');
        Owner::findOrFail($id)->delete(); // ソフトデリート

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナー情報を削除しました。',
            'status' => 'alert'
        ]);
    }

    // Soft Deleteされたユーザー一覧
    public function expiredOwnerIndex() {
        $expiredOwners = Owner::onlyTrashed()->get();
        return view('admin.expired-owners', compact('expiredOwners'));
    }

    // Soft Deleteされたユーザーを物理削除
    public function expiredOwnerDestroy($id) {
        Owner::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.expired-owners.index')->with([
            'message' => '完全に削除されました。',
            'status' => 'alert'
        ]);
    }
}
