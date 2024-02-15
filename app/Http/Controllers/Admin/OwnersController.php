<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner; // Eloquant エロくアント
use Illuminate\Support\Facades\DB; // QueryBuilder クエリビルダー
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


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


        $owners = Owner::select('id', 'name', 'email', 'created_at')->get();
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
        Owner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return redirect()
        ->route('admin.owners.index')
        ->with('message', 'オーナー登録を実施しました。');
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
        ->with('message', 'オーナー情報を更新しました。');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
