<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use  Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //内容受け取り、変数に入れる 今回は内容直貼り
        // $items = Item::select('id','name','price', 'is_selling')->get();
        return Inertia::render('Items/Index',[
            'items' => Item::select('id','name','price', 'is_selling')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Items/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemRequest $request)
    {
        Item::create([
            'name' => $request->name,
            'memo' => $request->memo,
            'price' => $request->price,
        ]);


        return to_route('items.index')
         ->with([
                'message' => '登録しました。',
                'status' => 'success'
            ]);;//商品一覧に戻る
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item) //(Item $item)＝idだけでなく、item事態の中身も見れる
    {
        // dd($item);
        return Inertia::render('Items/Show', [
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
         return Inertia::render('Items/Edit', [
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateItemRequest  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
       // dd($item->name＝入っている名前, $request->name＝入力しなおした名前);
       $item->name = $request->name;
       $item->memo = $request->memo;
       $item->price = $request->price;
       $item->is_selling = $request->is_selling;
       $item->save();

       return to_route('items.index')
       ->with([
                'message' => '更新しました。',
                'status' => 'success'
            ]);;//商品一覧に戻る
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return to_route('items.index')
       ->with([
                'message' => '削除しました。',
                'status' => 'danger'
            ]);;//商品一覧に戻る
    }
}
