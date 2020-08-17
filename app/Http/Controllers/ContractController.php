<?php

namespace App\Http\Controllers;

use App\Http\Resources\Contract as ContractResource;
use App\Repositories\ContractRepository;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    protected $contracts;

    public function __construct(ContractRepository $contracts)
    {
        $this->contracts = $contracts;
    }

    public function index() {
        $contracts = $this->contracts->all();

        return ContractResource::collection($contracts);
    }
}
