<?php

namespace App\Domain\Transaction\Presentation\API\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Transaction\Presentation\API\Requests\CreateTransactionRequest;
use App\Domain\Transaction\Application\Actions\CreateTransactionAction;
use Exception;

class TransactionCommandController extends Controller
{
    public function __construct(
        protected CreateTransactionAction $createTransactionAction
    ) {}

    public function store(CreateTransactionRequest $request)
    {
        try {
            $transaction = $this->createTransactionAction->execute($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully.',
                'data' => $transaction,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
