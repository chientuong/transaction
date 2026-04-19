<?php

namespace Source\Domain\Transaction\Presentation\API\Controllers;

use App\Http\Controllers\Controller;
use Source\Domain\Transaction\Application\Actions\GetActivePaymentPrefixesAction;
use Illuminate\Http\JsonResponse;

class PaymentPrefixQueryController extends Controller
{
    public function getActive(GetActivePaymentPrefixesAction $action): JsonResponse
    {
        $prefixes = $action->execute();
        
        return response()->json([
            'success' => true,
            'data' => $prefixes
        ]);
    }
}
