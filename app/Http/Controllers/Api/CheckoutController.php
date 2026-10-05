<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Request;
use App\Http\Response;
use App\Services\Api\CheckoutService;

class CheckoutController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected CheckoutService $service
    ) {}

    public function processCheckout(Request $request): Response
    { 
        $user     = $request->user();
        $subtotal = (float) $request->input('subtotal');
        $tax      = (float) $request->input('tax');
        $discount = (float) $request->input('discount');
        $shipping = (float) $request->input('shipping');
        $total    = (float) $request->input('total');
        $address  = $request->input('address');
        $items    = $request->input('items');

        $result = $this->service->processCheckout($subtotal, $tax, $discount, $shipping, $total, $address, $items, $user);

        return $this->response->json($result->toArray(), $result->status());
    }
}
