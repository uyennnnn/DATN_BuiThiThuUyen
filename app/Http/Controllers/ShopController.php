<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateShopRequest;
use App\Models\Option;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function index()
    {
        $shop = Shop::first();

        return Inertia::render('Admin/Shop/Index', [
            'shop_info' => $shop,
        ]);
    }

    public function migrate(Request $request)
    {
        $result = Artisan::call('migrate:subdomain');

        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $config = new Option();
            $config->initData($request->input('shop'));
            // Shop::create($request->input('shop'));
            User::create($request->input('admin'));

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['success' => false]);
        }
    }

    public function update(UpdateShopRequest $request)
    {
        $shop = Shop::first();
        if ($shop) {
            $shop->update($request->input('shop'));
        } else {
            $shop = Shop::create($request->input('shop'));
        }
        $user = Auth::user();
        $user->update($request->input('admin'));
        return redirect()->route('shop.update_success');
    }


    public function success()
    {
        return Inertia::render('Admin/Shop/Success');
    }
}
