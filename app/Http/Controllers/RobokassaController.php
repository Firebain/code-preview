<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ServiceRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\SubscriptionRepository;
use App\Http\Requests\OrderRequest;

use Illuminate\Support\Facades\Log;

class RobokassaController extends Controller
{
    protected $services;

    protected $transactions;

    protected $subscriptions;

    public function __construct() {
        $this->services = app(ServiceRepository::class);

        $this->transactions = app(TransactionRepository::class);

        $this->subscriptions = app(SubscriptionRepository::class);
    }

    public function index(OrderRequest $request) {
        $user = $request->user();

        if (!$request->has('main') && !$request->additional) {
            return '#';
        }

        $is_test = config("robokassa.test_mode");

        $mrh_login = config("robokassa.login");
        $mrh_pass1 = config("robokassa.passwords.first");

        $ids = $request->additional;
        $ids[] = $request->main;

        $services = $this->services->find($ids, true);

        $additional = $services->get('additional') ?: collect([]);

        if ($request->has('main')) {
            $main = $services->get('main')->first();

            $main_service_price = $main->price;

            if ($user->promocode) {
                $main_service_price = ($main->price / 100) * (100 - $user->promocode->discount_percent);
            }

            $additional_services_price = $additional
                ->reduce(function($acc, $service) {
                    return $acc + $service->price;
                }, 0);

            $out_summ = $main_service_price + $additional_services_price;

            $receipt['items'] = [
                'name' => $main->name,
                'quantity' => 1,
                'sum' => (int) $main_service_price,
            ];
        } else {
            $additional_services_price = $additional
                ->reduce(function($acc, $service) {
                    return $acc + $service->price;
                }, 0);

            $out_summ = $additional_services_price;
        }

        $transaction = $this->transactions->create(
            [
                'user_id' => $user->id,
                'sum' => $out_summ,
                'main_service_id' => $request->has('main') ? $services->get('main')->first()->id : null,
            ],
            $additional
        );

        $inv_id = $transaction->id;

        foreach($additional as $service) {
            $receipt['items'][] = [
                'name' => $service->name,
                'quantity' => 1,
                'sum' => $service->price,
            ];
        }

        $receipt = json_encode($receipt);

        $crc = md5("$mrh_login:$out_summ:$inv_id:$receipt:$mrh_pass1");

        $receipt = urlencode($receipt);

        $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=$mrh_login&".
               "OutSum=$out_summ&InvId=$inv_id&Receipt=$receipt&SignatureValue=$crc";

        if ($is_test) {
            $url .= '&IsTest=1';
        }

        return $url;
    }

    public function hook(Request $request) {
        $request->validate([
            'OutSum' => ['required', 'string'],
            'InvId' => ['required', 'string'],
            'SignatureValue' => ['required', 'string']
        ]);

        $mrh_pass2 = config("robokassa.passwords.second");

        $out_summ = $request->OutSum;
        $inv_id = $request->InvId;

        $crc = md5("$out_summ:$inv_id:$mrh_pass2");

        $signed = strcasecmp($crc, $request->SignatureValue) === 0;

        if ($signed) {
            $transaction = $this->transactions->confirm($inv_id);

            $user = $transaction->user;

            Log::info("New request from robokassa", [
                'request' => $request->all(),
                'transaction' => $transaction,
                'user' => $user
            ]);

            if ($user->has_subscription) {
                $this->subscriptions->renew($transaction, $user->subscription);
            } else {
                if ($transaction->main_service) {
                    $user->promocode_id = null;
                    $user->save();

                    $this->subscriptions->create($transaction);
                } else {
                    $this->subscriptions->renewOldSubscription($transaction);
                }
            }

            if (!$user->is_master) {
                $main_package_is_master = $transaction->main_service !== null && $transaction->main_service->key === "full";

                $additional_packages_has_master = $transaction->additional_services->contains(function ($service) {
                    return $service->key === "game_practice";
                });

                $user->is_master = $main_package_is_master || $additional_packages_has_master;
                $user->save();
            }

            return response("OK$inv_id", 200)
                ->header('Content-Type', 'text/plain');;
        }

        Log::error("Wrong hash from robokassa", [
            'request' => $request->all()
        ]);

        abort(403);
    }
}
