<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected WalletService $service
    ) {}

    public function updateDetails(Request $request): Response
    { 
        $userId  = $request->user()['id'];
        $account = $request->input('account');
        $bank    = $request->input('bank');
        $code    = $request->input('code');
        $result = $this->service->updateDetails($account, $bank, $code, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function createPayment(Request $request): Response
    { 
        $userId = $request->user()['id'];
        $amount = $request->input('amount');
        $result = $this->service->createPayment($amount, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function redeemFunds(Request $request): Response
    { 
        $itemId  = $request->input('itemId');
        $storeId = $request->input('storeId');
        $status  = $request->input('status');
        $result = $this->service->redeemFunds($itemId, $storeId, $status);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function requestFunds(Request $request): Response
    {
        $userId    = $request->user()['id'];
        $amount    = $request->input('amount');
        $narration = $request->input('narration');
        $result = $this->service->requestFunds($amount, $narration, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function singleTransfer(Request $request): Response
    {
        $name      = $request->input('name');
        $bank      = $request->input('bank');
        $account   = $request->input('account');
        $amount    = $request->input('amount');
        $narration = $request->input('narration');
        $currency  = $request->input('currency');
        $reference = $request->input('reference');

        $result = $this->service->singleTransfer(
            $name,
            $bank,
            $account,
            $amount,
            $narration,
            $currency,
            $reference
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function bulkTransfer(Request $request): Response
    {
        $title = $request->input('title');
        $data  = $request->input('bulk_data');
        $result = $this->service->bulkTransfer($title, $data);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getByReference(Request $request): Response
    {
        $type      = $request->input('type');
        $reference = $request->input('reference');
        $result = $this->service->getByReference($type, $reference);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getPaymentsByUser(Request $request): Response
    {
        $userId = $request->user()['id'];
        $type   = $request->input('type');
        $page   = $request->input('page');
        $result = $this->service->getPaymentsByUser($type, $page, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getPaymentsByType(Request $request): Response
    {
        $table = $request->input('table');
        $page  = $request->input('page');
        $result = $this->service->getPaymentsByType($table, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getPaymentsByStatus(Request $request): Response
    {
        $table  = $request->input('table');
        $column = $request->input('column');
        $status = $request->input('status');
        $page   = $request->input('page');
        $result = $this->service->getPaymentsByStatus($table, $column, $status, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getPayoutsByStatus(Request $request): Response
    {
        $status = $request->input('status');
        $page   = $request->input('page');
        $result = $this->service->getPayoutsByStatus($status, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function verifyPayment(Request $request): Response
    {
        $paymentId = $request->input('id');
        $reference = $request->input('reference');
        $result = $this->service->verifyPayment($paymentId, $reference);

        return $this->response->json($result->toArray(), $result->status());
    }
}
