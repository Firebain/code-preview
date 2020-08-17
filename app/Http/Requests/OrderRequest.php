<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Service;
use Illuminate\Validation\Rule;
use App\Repositories\ServiceRepository;
use App\Repositories\SubscriptionRepository;

class OrderRequest extends FormRequest
{
    protected $services;

    public function __construct(ServiceRepository $services) {
        $this->services = $services;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'main' => [
                // Обязательно если у пользователя нет подписки и небыло других подписок
                Rule::requiredIf(!$this->user()->has_subscription && $this->user()->subscriptions->isEmpty()),
                function($attribute, $value, $fail) {
                    $service = $this->services->find($value);

                    if ($service === null || $service->type !== Service::MAIN_TYPE) {
                        $fail("Неверное значение главного пакета");
                    }
                }
            ],
            'additional' => [
                'nullable',
                'array',
                function($attribute, $value, $fail) {
                    $services = $this->services->find($value);

                    $has_incorrect_type = $services->contains(function($value) {
                        return $value->type !== Service::ADDITIONAL_TYPE;
                    });

                    if ($has_incorrect_type) {
                        $fail("Неверное значение дополнительных пакетов");
                    }
                }
            ]
        ];
    }
}
