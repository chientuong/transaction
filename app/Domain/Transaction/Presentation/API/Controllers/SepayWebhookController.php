<?php

namespace App\Domain\Transaction\Presentation\API\Controllers;

use App\Domain\System\Infrastructure\Models\Setting;
use App\Domain\Transaction\Application\Actions\ProcessSepayWebhookAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class SepayWebhookController extends Controller
{
    public function __construct(
        protected ProcessSepayWebhookAction $processAction
    ) {}

    public function handle(Request $request): JsonResponse
    {
        // 1. Authenticate the webhook
        $providedToken = $request->header('Authorization');
        
        // SePay often sends Bearer Token or just a token string in Authorization header.
        // We'll compare it against the sepay_auth_token in settings.
        $storedToken = Setting::get('sepay_auth_token');

        if (!$storedToken || !str_contains((string)$providedToken, $storedToken)) {
             return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = $request->all();

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No data'
            ], 400);
        }

        try {
            $this->processAction->execute($data);
            
            return response()->json([
                'success' => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
