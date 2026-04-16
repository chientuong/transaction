<?php

namespace App\Domain\Transaction\Presentation\API\Controllers;

use App\Domain\System\Infrastructure\Models\Setting;
use App\Domain\Transaction\Application\Actions\ProcessSepayWebhookAction;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SepayWebhookController extends Controller
{
    public function __construct(
        protected ProcessSepayWebhookAction $processAction
    ) {}

    public function handle(Request $request): JsonResponse
    {
        // 1. Authenticate the webhook
        // SePay sends API Key with header: "Authorization: Apikey API_KEY"
        $providedToken = $request->header('Authorization');

        $storedToken = Setting::get('api_key_sepay');

        if (! $storedToken || $providedToken !== "Apikey {$storedToken}") {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $request->all();

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No data',
            ], 400);
        }

        try {
            $this->processAction->execute($data);

            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
