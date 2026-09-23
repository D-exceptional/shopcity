<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Request;
use App\Http\Response;
use App\Services\Web\HomeService;

class HomeController extends Controller
{
    public function __construct(
        protected Response $response,
        protected HomeService $service
    ) {}

    public function about(Request $request): Response
    {
        return $this->response->view('public.about');
    }

    // The url for accessing this is: /brands
    public function brands(Request $request): Response
    {
        $page = $request->input('page') ?? 1; 
        $viewData = $this->service->brands($page);

        return $this->response->view('public.brands', $viewData);
    }

    // The url for accessing this is: /brands/page/{page}
    public function brandsPaginated(Request $request): Response
    {
        $page = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->brands($page);

        return $this->response->view('public.brands', $viewData);
    }

    public function cart(Request $request): Response
    { 
        $userId = $request->user()['id'] ?? null; 
        $viewData = $this->service->cart($userId);

        return $this->response->view('public.cart', $viewData);
    }

    public function checkout(Request $request): Response
    {
        $userId = $request->user()['id'] ?? null; 
        $viewData = $this->service->checkout($userId);

        return $this->response->view('public.checkout', $viewData);
    }

    public function contact(Request $request): Response
    {
        return $this->response->view('public.contact');
    }

    public function delivery(Request $request): Response
    {
        return $this->response->view('public.delivery_policy');
    }

    public function faq(Request $request): Response
    {
        return $this->response->view('public.faq');
    }

    // The url for accessing this is: /
    public function home(Request $request): Response
    {
        $page = $request->input('page') ?? 1; 
        $viewData = $this->service->home($page);

        return $this->response->view('public.home', $viewData);
    }

    // The url for accessing this is: /page/{page}
    public function homePaginated(Request $request): Response
    {
        $page = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->home($page);

        return $this->response->view('public.home', $viewData);
    }

    public function orderView(Request $request): Response
    {
        $orderId = (int)$request->route('id'); 
        $viewData = $this->service->orderView($orderId);

        return $this->response->view('public.order_details', $viewData);
    }

    // The url for accessing this is: /orders
    public function orders(Request $request): Response
    {
        $userId = $request->user()['id'] ?? null; 
        $page   = $request->input('page') ?? 1; 
        $viewData = $this->service->orders($userId, $page);

        return $this->response->view('public.orders', $viewData);
    }

    // The url for accessing this is: /orders/page/{page}
    public function ordersPaginated(Request $request): Response
    {
        $userId = $request->user()['id'] ?? null; 
        $page   = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->orders($userId, $page);

        return $this->response->view('public.orders', $viewData);
    }

    public function privacy(Request $request): Response
    {
        return $this->response->view('public.privacy_policy');
    }

    public function productView(Request $request): Response
    {
        $productId = (int)$request->route('id') ?? null; 
        $viewData = $this->service->productView($productId);

        return $this->response->view('public.product_details', $viewData);
    }

    // The url for accessing this is: /products/{filter}/{value}/page/{page}
    public function productList(Request $request): Response
    {
        $filter  = $request->route('filter') ?? null; 
        $value   = $request->route('value') ?? null; 
        $page    = (int)$request->route('page') ?? null; 
        $viewData = $this->service->productList($filter, $value, $page);

        return $this->response->view('public.product_list', $viewData);
    }

    // The url for accessing this is: /product/{search}/search
    public function productSearch(Request $request): Response
    {
        $search = $request->route('search') ?? null; 
        $page    = $request->input('page') ?? 1; 
        $viewData = $this->service->productSearch($search, $page);

        return $this->response->view('public.product_search', $viewData);
    }

    // The url for accessing this is: /product/{search}/search/page/{page}
    public function productSearchPaginated(Request $request): Response
    {
        $search  = $request->route('search') ?? null; 
        $page    = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->productSearch($search, $page);

        return $this->response->view('public.product_search', $viewData);
    }

    // The url for accessing this is: /store/{id}/{filter}/{value}/page/{page}
    public function productStore(Request $request): Response
    {
        $storeId = (int)$request->route('id') ?? null; 
        $filter  = $request->route('filter') ?? null; 
        $value   = $request->route('value') ?? null; 
        $page    = (int)$request->route('page') ?? null; 
        $viewData = $this->service->productStore($storeId, $filter, $value, $page);

        return $this->response->view('public.product_store', $viewData);
    }

    public function profile(Request $request): Response
    {
        $userId = $request->user()['id'] ?? null; 
        $viewData = $this->service->profile($userId);

        return $this->response->view('public.profile', $viewData);
    }

    public function returns(Request $request): Response
    {
        return $this->response->view('public.returns_policy');
    }

    // The url for accessing this is: /store/{id}
    public function store(Request $request): Response
    {
        $storeId = (int)$request->route('id') ?? null; 
        $page    = (int)$request->input('page') ?? 1; 
        $viewData = $this->service->store($storeId, $page);

        return $this->response->view('public.store', $viewData);
    }

    // The url for accessing this is: /store/{id}/page/{page}
    public function storePaginated(Request $request): Response
    {
        $storeId = (int)$request->route('id') ?? null; 
        $page    = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->store($storeId, $page);

        return $this->response->view('public.store', $viewData);
    }

    public function support(Request $request): Response
    {
        return $this->response->view('public.support');
    }

    public function terms(Request $request): Response
    {
        return $this->response->view('public.terms_of_service');
    }

    public function testimonials(Request $request): Response
    {
        return $this->response->view('public.testimonials');
    }

    public function track(Request $request): Response
    {
        return $this->response->view('public.track_order');
    }

    public function wallet(Request $request): Response
    {
        $userId = $request->user()['id'] ?? null; 
        $viewData = $this->service->wallet($userId);

        return $this->response->view('public.wallet', $viewData);
    }

    public function warranty(Request $request): Response
    {
        return $this->response->view('public.warranty');
    }

    // The url for accessing this is: /wishlist
    public function wishlist(Request $request): Response
    {
        $userId = $request->user()['id'] ?? null; 
        $page   = $request->input('page') ?? 1; 
        $viewData = $this->service->wishlist($userId, $page);

        return $this->response->view('public.wishlist', $viewData);
    }

    // The url for accessing this is: /wishlist/page/{page}
    public function wishlistPaginated(Request $request): Response
    {
        $userId = $request->user()['id'] ?? null; 
        $page   = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->wishlist($userId, $page);

        return $this->response->view('public.wishlist', $viewData);
    }
}
