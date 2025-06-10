<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InertiaTest;

class InertiaTestController extends Controller
{
    public function index()
    {
        return Inertia::render('Inertia/Index', [//Inertia/Indexコンポーネントを指定
            'blogs' => InertiaTest::all() //複数形 => すべて取得
        ]);//[Inertia.indexのリソースにわたる]
    }

    public function create()
    {
        return Inertia::render('Inertia/Create');
    }

    public function show($id)
    {
        // dd($id); 値見る
        return Inertia::render('Inertia/Show',
        [
            'id' => $id,
            //引数の値view側に渡せる
            //コントローラーキー　=> バリュー
            'blog' => InertiaTest::findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {
          //バリデーション [Create.vue]
        $request->validate([
            'title' => ['required', 'max:20'],
            'content' => ['required'],
        ]);

        $inertiaTest = new InertiaTest; //読み込んだモデルをインスタンス化
        //ここからInertiaTestの中身記述
        $inertiaTest->title = $request->title;
        $inertiaTest->content = $request->content;
        $inertiaTest->save();

        //to_routeでInertia.indexに飛ばす
        //return Inertia::location(route('inertia.index'));?

        return to_route('inertia.index')
        ->with([
            'message' => '登録しました。'
        ]);
    }

    public function delete($id)
    {
        //ここで削除可能に、Laravelの機能
        $book = InertiaTest::findOrFail($id);
        $book->delete();

        return to_route('inertia.index')
        ->with([
            'message' => '削除しました。'
        ]);
    }
}
