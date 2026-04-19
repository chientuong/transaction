<?php

namespace Source\Domain\Transaction\Presentation\API\Controllers;

use App\Http\Controllers\Controller;
use Source\Domain\Transaction\Application\Actions\GetActiveBankAccountsAction;
use Illuminate\Http\JsonResponse;

class BankAccountQueryController extends Controller
{
    public function getActive(GetActiveBankAccountsAction $action): JsonResponse
    {
        $accounts = $action->execute();
        
        return response()->json([
            'success' => true,
            'data' => $accounts
        ]);
    }
}
