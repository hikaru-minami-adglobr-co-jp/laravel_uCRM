<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use Inertia\Inertia;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //ここのOrderですでに4つテーブルJOINし小計出した状態になる。
        // dd(Order::paginate(500));
        $orders = Order::groupBy('id')
        ->selectRaw('id,sum(subtotal) as total, customer_name, status, created_at')
        ->paginate(50);

        // dd($orders);

        return Inertia::render('Purchases/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //  $customers = Customer::select('id', 'name', 'kana')->get();
        $items = Item::select('id', 'name', 'price')
        ->where('is_selling', true)//販売中のものだけ取得できる。
        ->get();

        return Inertia::render('Purchases/Create', [
            // 'customers' => $customers,
            'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseRequest $request)
    {
        // dd($request);

        DB::beginTransaction();
            try{
                $purchase = Purchase::create([
                'customer_id' => $request->customer_id,
                'status' => $request->status,
            ]);
            foreach($request->items as $item){
                $purchase->items()->attach( $purchase->id, [
                'item_id' => $item['id'],
                'quantity' => $item['quantity']
                ]);
                    }

                    DB::commit();

                    return to_route('dashboard');

        } catch(\Exception $e){
            DB::rollback();
        }

        //purchaseテーブルに保存
        $purchase = Purchase::create([
            'customer_id' => $request->customer_id,
            'status' => $request->status,
        ]);
        foreach($request->items as $item){
            $purchase->items()->attach( $purchase->id, [
            'item_id' => $item['id'],
            'quantity' => $item['quantity']
            ]);
        } return to_route('dashboard');}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        //小計
        $items = Order::where('id', $purchase->id)->get();
        //合計取得
        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id,sum(subtotal) as total, customer_name, status, created_at')
        ->get();

        // dd($items, $order);

        return Inertia::render('Purchases/Show', [
            'items' => $items,
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        //Vue側分から割ったって来るid指定、中間テーブルの情報から1件だけ購買IDの情報取る
        $purchase = Purchase::find($purchase->id);

        //すべての商品情報getで取得
        $allItems = Item::select('id', 'name', 'price')
        ->get();
        //から配列に情報入れていく
        $items = [];

        foreach($allItems as $allItem) {//1件づつのアイテムの情報取得
            $quantity = 0;//中間テーブルの情報あったら更新してから配列を更新していく。
            foreach($purchase->items as $item){//1件づつ中間テーブルに入っているもの情報取得
                if($allItem->id === $item->id){//id同じ、中間テーブルにidあったら
                    $quantity = $item->pivot->quantity;//quantity書き換え、中間テーブルの数量の情報所得
                }
            }
            array_push($items, [
                'id' => $allItem->id,
                'name' => $allItem->name,
                'price' => $allItem->price,
                'quantity' => $quantity,
            ]);
        }
        // dd($items);

         //合計取得
        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id,customer_id, customer_name, status, created_at')
        ->get();

        return Inertia::render('Purchases/Edit', [
            'items' => $items,
            'order' => $order
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {

        DB::beginTransaction();

        try{
            // dd($request,$purchase);
            $purchase->status = $request->status;
            $purchase->save();//statusのみ更新

            //中間テーブル
            $items = [];

            foreach($request->items as $item){
                $items = $items + [
                    $item['id'] => [
                        'quantity' => $item['quantity']
                    ]
                ];
            }

            // dd($items);
            $purchase->items()->sync($items);

            DB::commit();//確定させる
            return to_route('dashboard');
        } catch(\Exception $e){
            DB::rollback();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
