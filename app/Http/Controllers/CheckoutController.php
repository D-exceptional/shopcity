<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\CheckoutService;

class CheckoutController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected CheckoutService $service
    ) {}

    public function processCheckout(Request $request): Response
    { 
        $user     = $request->user();
        $subtotal = $request->input('subtotal');
        $tax      = $request->input('tax');
        $discount = $request->input('discount');
        $shipping = $request->input('shipping');
        $total    = $request->input('total');
        $address  = $request->input('address');
        $items    = $request->input('items');

        $result = $this->service->processCheckout($subtotal, $tax, $discount, $shipping, $total, $address, $items, $user);

        return $this->response->json($result->toArray(), $result->status());
    }
}
