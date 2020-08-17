<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ServiceRepository;
use App\Http\Requests\OrderRequest;

class TinkoffController extends Controller
{
    protected $services;

    public function __construct() {
        $this->services = app(ServiceRepository::class);
    }

    public function index(OrderRequest $request) {
        $request->validate([
            'promocode' => ['nullable', 'string']
        ]);

        $user = $request->user();

        $fields = [[ 'key' => 'shopId', 'value' => config('tinkoff.shopId') ]];

        if (config('tinkoff.showcaseId') !== null) {
            $fields[] = [ 'key' => 'showcaseId', 'value' => config('tinkoff.showcaseId') ];
        }

        if ($request->promocode !== null) {
            $fields[] = [ 'key' => 'promoCode', 'value' => $request->promocode ];
        }

        $ids = $request->additional;
        $ids[] = $request->main;

        $services = $this->services->find($ids, true);

        $additional = $services->get('additional') ?: collect([]);

        $item_number = 0;

        if ($request->has('main')) {
            $main = $services->get('main')->first();

            $main_service_price = $main->price;

            if ($user->promocode) {
                $main_service_price = ($main->price / 100) * (100 - $user->promocode->discount_percent);
            }

            $fields[] = [ 'key' => "itemName_{$item_number}", 'value' => "Пакет {$main->name}" ];
            $fields[] = [ 'key' => "itemQuantity_{$item_number}", 'value' => 1 ];
            $fields[] = [ 'key' => "itemPrice_{$item_number}", 'value' => $main_service_price ];

            $item_number++;

            $additional_services_price = $additional
                ->reduce(function($acc, $service) {
                    return $acc + $service->price;
                }, 0);

            $out_summ = $main_service_price + $additional_services_price;
        } else {
            $additional_services_price = $additional
                ->reduce(function($acc, $service) {
                    return $acc + $service->price;
                }, 0);

            $out_summ = $additional_services_price;
        }

        foreach ($additional as $service) {
            $fields[] = [ 'key' => "itemName_{$item_number}", 'value' => $service->name ];
            $fields[] = [ 'key' => "itemQuantity_{$item_number}", 'value' => 1 ];
            $fields[] = [ 'key' => "itemPrice_{$item_number}", 'value' => $service->price ];

            $item_number++;
        }

        $fields[] = [ 'key' => 'sum', 'value' => $out_summ ];
        $fields[] = [ 'key' => 'customerNumber', 'value' => $user->id ];
        $fields[] = [ 'key' => 'customerEmail', 'value' => $user->email ];

        return $fields;
    }
}
