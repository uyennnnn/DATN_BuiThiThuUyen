<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateShopRequest;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Shop/Index', [
            'shop_info' => Option::getConfig(),
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

    public function init(Request $request)
    {
        DB::beginTransaction();
        try {
            $config = new Option();
            $config->initData($request->input('shop'));
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
        $config = new Option();
        $config->initData($request->input('shop'));

        $user = Auth::user();
        $user->update($request->input('admin'));

        return redirect()->route('shop.update_success');
    }

    public function success()
    {
        return Inertia::render('Admin/Shop/Success');
    }
}
