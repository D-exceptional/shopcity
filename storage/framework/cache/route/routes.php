<?php

return array (
  'api' => 
  array (
    'static' => 
    array (
      'GET' => 
      array (
        '/api/test/ping' => 
        array (
          'method' => 'GET',
          'path' => '/api/test/ping',
          'controller' => 'App\\Http\\Controllers\\Api\\TestController',
          'action' => 'ping',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'api.test.ping',
          'pattern' => NULL,
        ),
        '/api/test/redis' => 
        array (
          'method' => 'GET',
          'path' => '/api/test/redis',
          'controller' => 'App\\Http\\Controllers\\Api\\TestController',
          'action' => 'redis',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'api.test.redis',
          'pattern' => NULL,
        ),
        '/api/test/event' => 
        array (
          'method' => 'GET',
          'path' => '/api/test/event',
          'controller' => 'App\\Http\\Controllers\\Api\\TestController',
          'action' => 'event',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'api.test.event',
          'pattern' => NULL,
        ),
        '/api/cart' => 
        array (
          'method' => 'GET',
          'path' => '/api/cart',
          'controller' => 'App\\Http\\Controllers\\Api\\CartController',
          'action' => 'view',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
          ),
          'name' => 'api.cart.get',
          'pattern' => NULL,
        ),
        '/api/cart/count-user' => 
        array (
          'method' => 'GET',
          'path' => '/api/cart/count-user',
          'controller' => 'App\\Http\\Controllers\\Api\\CartController',
          'action' => 'countUser',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
          ),
          'name' => 'api.cart.countuser',
          'pattern' => NULL,
        ),
        '/api/cart/count-all' => 
        array (
          'method' => 'GET',
          'path' => '/api/cart/count-all',
          'controller' => 'App\\Http\\Controllers\\Api\\CartController',
          'action' => 'countAll',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
          ),
          'name' => 'api.cart.countall',
          'pattern' => NULL,
        ),
        '/api/cart/users' => 
        array (
          'method' => 'GET',
          'path' => '/api/cart/users',
          'controller' => 'App\\Http\\Controllers\\Api\\CartController',
          'action' => 'getCartUsers',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
          ),
          'name' => 'api.cart.getusers',
          'pattern' => NULL,
        ),
        '/api/category' => 
        array (
          'method' => 'GET',
          'path' => '/api/category',
          'controller' => 'App\\Http\\Controllers\\Api\\CategoryController',
          'action' => 'all',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'api.category.all',
          'pattern' => NULL,
        ),
        '/api/category/group' => 
        array (
          'method' => 'GET',
          'path' => '/api/category/group',
          'controller' => 'App\\Http\\Controllers\\Api\\CategoryController',
          'action' => 'group',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'api.category.group',
          'pattern' => NULL,
        ),
        '/api/category/count' => 
        array (
          'method' => 'GET',
          'path' => '/api/category/count',
          'controller' => 'App\\Http\\Controllers\\Api\\CategoryController',
          'action' => 'count',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
          ),
          'name' => 'api.category.count',
          'pattern' => NULL,
        ),
        '/api/mail/count' => 
        array (
          'method' => 'GET',
          'path' => '/api/mail/count',
          'controller' => 'App\\Http\\Controllers\\Api\\MailController',
          'action' => 'countMessages',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'api.mail.count',
          'pattern' => NULL,
        ),
        '/api/notification' => 
        array (
          'method' => 'GET',
          'path' => '/api/notification',
          'controller' => 'App\\Http\\Controllers\\Api\\NotificationController',
          'action' => 'fetchById',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'page' => 
                  array (
                    0 => 'number',
                  ),
                  'limit' => 
                  array (
                    0 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.notification.fetchid',
          'pattern' => NULL,
        ),
        '/api/notification/count-all' => 
        array (
          'method' => 'GET',
          'path' => '/api/notification/count-all',
          'controller' => 'App\\Http\\Controllers\\Api\\NotificationController',
          'action' => 'countAll',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
          ),
          'name' => 'api.notification.countall',
          'pattern' => NULL,
        ),
        '/api/notification/count-user' => 
        array (
          'method' => 'GET',
          'path' => '/api/notification/count-user',
          'controller' => 'App\\Http\\Controllers\\Api\\NotificationController',
          'action' => 'countAllById',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
          ),
          'name' => 'api.notification.countuser',
          'pattern' => NULL,
        ),
        '/api/notification/count-unread' => 
        array (
          'method' => 'GET',
          'path' => '/api/notification/count-unread',
          'controller' => 'App\\Http\\Controllers\\Api\\NotificationController',
          'action' => 'countUnreadById',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
          ),
          'name' => 'api.notification.countunread',
          'pattern' => NULL,
        ),
        '/api/notification/unread' => 
        array (
          'method' => 'GET',
          'path' => '/api/notification/unread',
          'controller' => 'App\\Http\\Controllers\\Api\\NotificationController',
          'action' => 'getUnread',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'page' => 
                  array (
                    0 => 'number',
                  ),
                  'limit' => 
                  array (
                    0 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.notification.getunread',
          'pattern' => NULL,
        ),
        '/api/order/sales-summary' => 
        array (
          'method' => 'GET',
          'path' => '/api/order/sales-summary',
          'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
          'action' => 'getSalesSummary',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'view' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'period' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'start' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'end' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.order.summary',
          'pattern' => NULL,
        ),
        '/api/product' => 
        array (
          'method' => 'GET',
          'path' => '/api/product',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findByAll',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'view' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.all',
          'pattern' => NULL,
        ),
        '/api/product/recent' => 
        array (
          'method' => 'GET',
          'path' => '/api/product/recent',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findNewArrivals',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'view' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.recent',
          'pattern' => NULL,
        ),
        '/api/product/featured' => 
        array (
          'method' => 'GET',
          'path' => '/api/product/featured',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findFeatured',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'view' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.featured',
          'pattern' => NULL,
        ),
        '/api/product/top-selling' => 
        array (
          'method' => 'GET',
          'path' => '/api/product/top-selling',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findTopSelling',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.topselling',
          'pattern' => NULL,
        ),
        '/api/product/price-range' => 
        array (
          'method' => 'GET',
          'path' => '/api/product/price-range',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findByPriceRange',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'min' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'max' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'view' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.pricerange',
          'pattern' => NULL,
        ),
        '/api/product/price-min' => 
        array (
          'method' => 'GET',
          'path' => '/api/product/price-min',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findByMinPrice',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'min' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'view' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.pricemin',
          'pattern' => NULL,
        ),
        '/api/product/price-max' => 
        array (
          'method' => 'GET',
          'path' => '/api/product/price-max',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findByMaxPrice',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'max' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'view' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.pricemax',
          'pattern' => NULL,
        ),
        '/api/product/category/group' => 
        array (
          'method' => 'GET',
          'path' => '/api/product/category/group',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
          'action' => 'findByGroupedCategory',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.product.categorygroup',
          'pattern' => NULL,
        ),
        '/api/store/count' => 
        array (
          'method' => 'GET',
          'path' => '/api/store/count',
          'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
          'action' => 'countStoresByStatus',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
          ),
          'name' => 'api.store.count',
          'pattern' => NULL,
        ),
        '/api/user/count' => 
        array (
          'method' => 'GET',
          'path' => '/api/user/count',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'count',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
          ),
          'name' => 'api.user.count',
          'pattern' => NULL,
        ),
        '/api/wallet/payment-reference' => 
        array (
          'method' => 'GET',
          'path' => '/api/wallet/payment-reference',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'getByReference',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'type' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'reference' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.getreference',
          'pattern' => NULL,
        ),
        '/api/wallet/payment-user' => 
        array (
          'method' => 'GET',
          'path' => '/api/wallet/payment-user',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'getPaymentsByUser',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'customer',
                  1 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'type' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.getuser',
          'pattern' => NULL,
        ),
        '/api/wallet/payment-type' => 
        array (
          'method' => 'GET',
          'path' => '/api/wallet/payment-type',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'getPaymentsByType',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'table' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.gettype',
          'pattern' => NULL,
        ),
        '/api/wallet/payment-status' => 
        array (
          'method' => 'GET',
          'path' => '/api/wallet/payment-status',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'getPaymentsByStatus',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'table' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'column' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'status' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.getstatus',
          'pattern' => NULL,
        ),
        '/api/wallet/payout-status' => 
        array (
          'method' => 'GET',
          'path' => '/api/wallet/payout-status',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'getPayoutsByStatus',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'status' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'page' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.getpayouts',
          'pattern' => NULL,
        ),
        '/api/wishlist' => 
        array (
          'method' => 'GET',
          'path' => '/api/wishlist',
          'controller' => 'App\\Http\\Controllers\\Api\\WishlistController',
          'action' => 'view',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
          ),
          'name' => 'api.wishlist.view',
          'pattern' => NULL,
        ),
      ),
      'PUT' => 
      array (
        '/api/cart/merge' => 
        array (
          'method' => 'PUT',
          'path' => '/api/cart/merge',
          'controller' => 'App\\Http\\Controllers\\Api\\CartController',
          'action' => 'merge',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'cart' => 
                  array (
                    0 => 'required',
                    1 => 'array',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.cart.merge',
          'pattern' => NULL,
        ),
        '/api/notification/mark-read' => 
        array (
          'method' => 'PUT',
          'path' => '/api/notification/mark-read',
          'controller' => 'App\\Http\\Controllers\\Api\\NotificationController',
          'action' => 'markAsRead',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
          ),
          'name' => 'api.notification.markread',
          'pattern' => NULL,
        ),
        '/api/user/password-reset' => 
        array (
          'method' => 'PUT',
          'path' => '/api/user/password-reset',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'reset',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'email' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'password' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'otp' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.passwordreset',
          'pattern' => NULL,
        ),
        '/api/user/update' => 
        array (
          'method' => 'PUT',
          'path' => '/api/user/update',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'update',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'firstname' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'lastname' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'contact' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.update',
          'pattern' => NULL,
        ),
        '/api/user/profile' => 
        array (
          'method' => 'PUT',
          'path' => '/api/user/profile',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'profile',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'avatar' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.profile',
          'pattern' => NULL,
        ),
        '/api/user/social' => 
        array (
          'method' => 'PUT',
          'path' => '/api/user/social',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'social',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'facebook' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'instagram' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'tiktok' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'twitter' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.social',
          'pattern' => NULL,
        ),
        '/api/user/password-change' => 
        array (
          'method' => 'PUT',
          'path' => '/api/user/password-change',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'password',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'password' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.passwordchange',
          'pattern' => NULL,
        ),
        '/api/user/billing' => 
        array (
          'method' => 'PUT',
          'path' => '/api/user/billing',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'billing',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'address' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'city' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'code' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.billing',
          'pattern' => NULL,
        ),
        '/api/wallet/details-update' => 
        array (
          'method' => 'PUT',
          'path' => '/api/wallet/details-update',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'updateDetails',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'account' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'bank' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'code' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.updatedetails',
          'pattern' => NULL,
        ),
        '/api/wishlist' => 
        array (
          'method' => 'PUT',
          'path' => '/api/wishlist',
          'controller' => 'App\\Http\\Controllers\\Api\\WishlistController',
          'action' => 'merge',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'wishlist' => 
                  array (
                    0 => 'required',
                    1 => 'array',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wishlist.merge',
          'pattern' => NULL,
        ),
      ),
      'DELETE' => 
      array (
        '/api/cart/clear' => 
        array (
          'method' => 'DELETE',
          'path' => '/api/cart/clear',
          'controller' => 'App\\Http\\Controllers\\Api\\CartController',
          'action' => 'clear',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
          ),
          'name' => 'api.cart.clear',
          'pattern' => NULL,
        ),
        '/api/media/bulk' => 
        array (
          'method' => 'DELETE',
          'path' => '/api/media/bulk',
          'controller' => 'App\\Http\\Controllers\\Api\\ProductMediaController',
          'action' => 'deleteBulk',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'urls' => 
                  array (
                    0 => 'required',
                    1 => 'array',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.media.deletebulk',
          'pattern' => NULL,
        ),
        '/api/user/unsubscribe' => 
        array (
          'method' => 'DELETE',
          'path' => '/api/user/unsubscribe',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'unsubscribe',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'token' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'device_id' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.unsubscribe',
          'pattern' => NULL,
        ),
        '/api/wishlist/clear' => 
        array (
          'method' => 'DELETE',
          'path' => '/api/wishlist/clear',
          'controller' => 'App\\Http\\Controllers\\Api\\WishlistController',
          'action' => 'clear',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
          ),
          'name' => 'api.wishlist.clear',
          'pattern' => NULL,
        ),
      ),
      'POST' => 
      array (
        '/api/category' => 
        array (
          'method' => 'POST',
          'path' => '/api/category',
          'controller' => 'App\\Http\\Controllers\\Api\\CategoryController',
          'action' => 'create',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'category' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.category.create',
          'pattern' => NULL,
        ),
        '/api/checkout' => 
        array (
          'method' => 'POST',
          'path' => '/api/checkout',
          'controller' => 'App\\Http\\Controllers\\Api\\CheckoutController',
          'action' => 'processCheckout',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'subtotal' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'tax' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'discount' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'shipping' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'total' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'address' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'items' => 
                  array (
                    0 => 'required',
                    1 => 'array',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.checkout.process',
          'pattern' => NULL,
        ),
        '/api/link' => 
        array (
          'method' => 'POST',
          'path' => '/api/link',
          'controller' => 'App\\Http\\Controllers\\Api\\LinkController',
          'action' => 'create',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                  1 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'productId' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'userId' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'short' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'long' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'code' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'status' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.link.create',
          'pattern' => NULL,
        ),
        '/api/mail/send' => 
        array (
          'method' => 'POST',
          'path' => '/api/mail/send',
          'controller' => 'App\\Http\\Controllers\\Api\\MailController',
          'action' => 'sendBulk',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
          ),
          'name' => 'api.mail.send',
          'pattern' => NULL,
        ),
        '/api/notification' => 
        array (
          'method' => 'POST',
          'path' => '/api/notification',
          'controller' => 'App\\Http\\Controllers\\Api\\NotificationController',
          'action' => 'create',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'details' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'type' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'receiver' => 
                  array (
                    0 => 'required',
                    1 => 'int',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.notification.create',
          'pattern' => NULL,
        ),
        '/api/store' => 
        array (
          'method' => 'POST',
          'path' => '/api/store',
          'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
          'action' => 'createStore',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'name' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'avatar' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'description' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'type' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'delivery' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.store.create',
          'pattern' => NULL,
        ),
        '/api/user/register' => 
        array (
          'method' => 'POST',
          'path' => '/api/user/register',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'register',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'firstname' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'lastname' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'email' => 
                  array (
                    0 => 'required',
                    1 => 'email',
                  ),
                  'contact' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                    2 => 'min:7',
                  ),
                  'country' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'password' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                    2 => 'min:6',
                  ),
                  'role' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'code' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'abbr' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'currency' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'state' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'creator' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.register',
          'pattern' => NULL,
        ),
        '/api/user/login' => 
        array (
          'method' => 'POST',
          'path' => '/api/user/login',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'login',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'email' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'password' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.login',
          'pattern' => NULL,
        ),
        '/api/user/otp' => 
        array (
          'method' => 'POST',
          'path' => '/api/user/otp',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'otp',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'email' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.otp',
          'pattern' => NULL,
        ),
        '/api/user/logout' => 
        array (
          'method' => 'POST',
          'path' => '/api/user/logout',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'logout',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'api.user.logout',
          'pattern' => NULL,
        ),
        '/api/user/contact' => 
        array (
          'method' => 'POST',
          'path' => '/api/user/contact',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'contact',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'name' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'email' => 
                  array (
                    0 => 'required',
                    1 => 'email',
                  ),
                  'contact' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'country' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'subject' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'message' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'code' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.contact',
          'pattern' => NULL,
        ),
        '/api/user/subscribe' => 
        array (
          'method' => 'POST',
          'path' => '/api/user/subscribe',
          'controller' => 'App\\Http\\Controllers\\Api\\UserController',
          'action' => 'subscribe',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'token' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'device_id' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.user.subscribe',
          'pattern' => NULL,
        ),
        '/api/wallet/fund' => 
        array (
          'method' => 'POST',
          'path' => '/api/wallet/fund',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'createPayment',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'user',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'amount' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.createpayment',
          'pattern' => NULL,
        ),
        '/api/wallet/payment-verify' => 
        array (
          'method' => 'POST',
          'path' => '/api/wallet/payment-verify',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'verifyPayment',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'id' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'reference' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.updatepayment',
          'pattern' => NULL,
        ),
        '/api/wallet/fund-redeem' => 
        array (
          'method' => 'POST',
          'path' => '/api/wallet/fund-redeem',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'redeemFunds',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'itemId' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'storeId' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'status' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.redeemfund',
          'pattern' => NULL,
        ),
        '/api/wallet/fund-request' => 
        array (
          'method' => 'POST',
          'path' => '/api/wallet/fund-request',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'requestFunds',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'amount' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'narration' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.requestfund',
          'pattern' => NULL,
        ),
        '/api/wallet/transfer-single' => 
        array (
          'method' => 'POST',
          'path' => '/api/wallet/transfer-single',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'singleTransfer',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'name' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'bank' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'account' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'amount' => 
                  array (
                    0 => 'required',
                    1 => 'number',
                  ),
                  'narration' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'currency' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'reference' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.singletransfer',
          'pattern' => NULL,
        ),
        '/api/wallet/transfer-bulk' => 
        array (
          'method' => 'POST',
          'path' => '/api/wallet/transfer-bulk',
          'controller' => 'App\\Http\\Controllers\\Api\\WalletController',
          'action' => 'bulkTransfer',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'api',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\JwtMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
              1 => 'handle',
              2 => 
              array (
                'rules' => 
                array (
                  'title' => 
                  array (
                    0 => 'required',
                    1 => 'string',
                  ),
                  'bulk_data' => 
                  array (
                    0 => 'required',
                    1 => 'array',
                  ),
                ),
              ),
            ),
          ),
          'name' => 'api.wallet.bulktransfer',
          'pattern' => NULL,
        ),
      ),
    ),
    'dynamic' => 
    array (
      'POST' => 
      array (
        'cart' => 
        array (
          0 => 
          array (
            'method' => 'POST',
            'path' => '/api/cart/product/{id}/quantity/{quantity}',
            'controller' => 'App\\Http\\Controllers\\Api\\CartController',
            'action' => 'add',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.cart.add',
            'pattern' => '#^/api/cart/product/(?P<id>[^/]+)/quantity/(?P<quantity>[^/]+)$#',
          ),
        ),
        'product' => 
        array (
          0 => 
          array (
            'method' => 'POST',
            'path' => '/api/product/store/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'create',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'name' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'description' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'category' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'subcategory' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'price' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'slash' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'stock' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'color' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'media' => 
                    array (
                      0 => 'required',
                      1 => 'array',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.create',
            'pattern' => '#^/api/product/store/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'POST',
            'path' => '/api/product/{id}/review',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'addReview',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'review' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'rating' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.review',
            'pattern' => '#^/api/product/(?P<id>[^/]+)/review$#',
          ),
        ),
        'store' => 
        array (
          0 => 
          array (
            'method' => 'POST',
            'path' => '/api/store/{id}/coupon',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'createCoupon',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'code' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'discount' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.store.createcoupon',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/coupon$#',
          ),
        ),
        'wishlist' => 
        array (
          0 => 
          array (
            'method' => 'POST',
            'path' => '/api/wishlist/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\WishlistController',
            'action' => 'add',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.wishlist.add',
            'pattern' => '#^/api/wishlist/product/(?P<id>[^/]+)$#',
          ),
        ),
      ),
      'PUT' => 
      array (
        'cart' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/cart/product/{id}/quantity/{quantity}',
            'controller' => 'App\\Http\\Controllers\\Api\\CartController',
            'action' => 'update',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.cart.update',
            'pattern' => '#^/api/cart/product/(?P<id>[^/]+)/quantity/(?P<quantity>[^/]+)$#',
          ),
        ),
        'category' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/category/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\CategoryController',
            'action' => 'update',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'name' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.category.update',
            'pattern' => '#^/api/category/(?P<id>[^/]+)$#',
          ),
        ),
        'link' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/link/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\LinkController',
            'action' => 'updateAll',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'status' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.link.update.all',
            'pattern' => '#^/api/link/product/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'PUT',
            'path' => '/api/link/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\LinkController',
            'action' => 'updateOne',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'status' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.link.update.one',
            'pattern' => '#^/api/link/(?P<id>[^/]+)$#',
          ),
        ),
        'media' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/media/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductMediaController',
            'action' => 'update',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'url' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.media.update',
            'pattern' => '#^/api/media/(?P<id>[^/]+)$#',
          ),
        ),
        'order' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/order/{id}/complete',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'completeOrder',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.complete',
            'pattern' => '#^/api/order/(?P<id>[^/]+)/complete$#',
          ),
          1 => 
          array (
            'method' => 'PUT',
            'path' => '/api/order/item/{id}/status/{status}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'updateItemStatus',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.itemupdate',
            'pattern' => '#^/api/order/item/(?P<id>[^/]+)/status/(?P<status>[^/]+)$#',
          ),
        ),
        'product' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'update',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'name' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'description' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'category' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'subcategory' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'price' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'slash' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'stock' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'color' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'reselling' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'commission' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'visibility' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.update',
            'pattern' => '#^/api/product/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'PUT',
            'path' => '/api/product/{id}/metadata',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'metadata',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'visibility' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'featured' => 
                    array (
                      0 => 'required',
                      1 => 'boolean',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.updatemetadata',
            'pattern' => '#^/api/product/(?P<id>[^/]+)/metadata$#',
          ),
        ),
        'store' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/store/coupon/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'updateCoupon',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'code' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'discount' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'status' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.store.updatecoupon',
            'pattern' => '#^/api/store/coupon/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'PUT',
            'path' => '/api/store/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'updateStoreDetails',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'name' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'description' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'delivery' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.store.updatedetails',
            'pattern' => '#^/api/store/(?P<id>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'PUT',
            'path' => '/api/store/{id}/socials',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'updateStoreSocials',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'facebook' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'instagram' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'tiktok' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'twitter' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.store.updatesocials',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/socials$#',
          ),
          3 => 
          array (
            'method' => 'PUT',
            'path' => '/api/store/{id}/avatar',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'updateStoreAvatar',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'url' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.store.updateavatar',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/avatar$#',
          ),
          4 => 
          array (
            'method' => 'PUT',
            'path' => '/api/store/{id}/status/{status}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'updateStoreStatus',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.updatestatus',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/status/(?P<status>[^/]+)$#',
          ),
        ),
        'user' => 
        array (
          0 => 
          array (
            'method' => 'PUT',
            'path' => '/api/user/{id}/status/{status}',
            'controller' => 'App\\Http\\Controllers\\Api\\UserController',
            'action' => 'status',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.user.status',
            'pattern' => '#^/api/user/(?P<id>[^/]+)/status/(?P<status>[^/]+)$#',
          ),
        ),
      ),
      'DELETE' => 
      array (
        'cart' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/cart/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\CartController',
            'action' => 'remove',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.cart.delete',
            'pattern' => '#^/api/cart/(?P<id>[^/]+)$#',
          ),
        ),
        'category' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/category/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\CategoryController',
            'action' => 'delete',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.category.delete',
            'pattern' => '#^/api/category/(?P<id>[^/]+)$#',
          ),
        ),
        'link' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/link/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\LinkController',
            'action' => 'deleteAll',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.link.delete.all',
            'pattern' => '#^/api/link/product/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/link/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\LinkController',
            'action' => 'deleteOne',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.link.delete.one',
            'pattern' => '#^/api/link/(?P<id>[^/]+)$#',
          ),
        ),
        'mail' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/mail/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\MailController',
            'action' => 'deleteMail',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.mail.delete',
            'pattern' => '#^/api/mail/(?P<id>[^/]+)$#',
          ),
        ),
        'media' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/media/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductMediaController',
            'action' => 'deleteAll',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.media.deleteall',
            'pattern' => '#^/api/media/product/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/media/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductMediaController',
            'action' => 'deleteOne',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.media.deleteone',
            'pattern' => '#^/api/media/(?P<id>[^/]+)$#',
          ),
        ),
        'order' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/order/{id}/cancel',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'cancelOrder',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.cancel',
            'pattern' => '#^/api/order/(?P<id>[^/]+)/cancel$#',
          ),
        ),
        'product' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'delete',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.product.delete',
            'pattern' => '#^/api/product/(?P<id>[^/]+)$#',
          ),
        ),
        'store' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/store/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'deleteStore',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.delete',
            'pattern' => '#^/api/store/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/store/coupon/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'deleteSingleCoupon',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.deletecoupon',
            'pattern' => '#^/api/store/coupon/(?P<id>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/store/{id}/coupon',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'deleteCouponByStore',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.deleteallcoupon',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/coupon$#',
          ),
        ),
        'user' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/user/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\UserController',
            'action' => 'delete',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.user.delete',
            'pattern' => '#^/api/user/(?P<id>[^/]+)$#',
          ),
        ),
        'wishlist' => 
        array (
          0 => 
          array (
            'method' => 'DELETE',
            'path' => '/api/wishlist/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\WishlistController',
            'action' => 'remove',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.wishlist.remove',
            'pattern' => '#^/api/wishlist/product/(?P<id>[^/]+)$#',
          ),
        ),
      ),
      'GET' => 
      array (
        'category' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/category/subcategories/{category}',
            'controller' => 'App\\Http\\Controllers\\Api\\CategoryController',
            'action' => 'fetch',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.category.fetch',
            'pattern' => '#^/api/category/subcategories/(?P<category>[^/]+)$#',
          ),
        ),
        'link' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/link/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\LinkController',
            'action' => 'findAll',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.link.all',
            'pattern' => '#^/api/link/product/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/api/link/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\LinkController',
            'action' => 'findOne',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                    1 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.link.one',
            'pattern' => '#^/api/link/(?P<id>[^/]+)$#',
          ),
        ),
        'mail' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/mail/inbox/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\MailController',
            'action' => 'getInbox',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'api.mail.inbox',
            'pattern' => '#^/api/mail/inbox/(?P<page>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/api/mail/outbox/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\MailController',
            'action' => 'getOutbox',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'api.mail.outbox',
            'pattern' => '#^/api/mail/outbox/(?P<page>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/api/mail/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\MailController',
            'action' => 'getMail',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'api.mail.get',
            'pattern' => '#^/api/mail/(?P<id>[^/]+)$#',
          ),
        ),
        'media' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/media/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductMediaController',
            'action' => 'findAll',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'api.media.getall',
            'pattern' => '#^/api/media/product/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/api/media/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductMediaController',
            'action' => 'findOne',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'api.media.getone',
            'pattern' => '#^/api/media/(?P<id>[^/]+)$#',
          ),
        ),
        'order' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/order/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'getOrder',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.getone',
            'pattern' => '#^/api/order/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/api/order/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'getAllOrders',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.getall',
            'pattern' => '#^/api/order/page/(?P<page>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/api/order/status/{status}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'getOrdersByStatus',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.status',
            'pattern' => '#^/api/order/status/(?P<status>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          3 => 
          array (
            'method' => 'GET',
            'path' => '/api/order/user/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'getUserOrders',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.user',
            'pattern' => '#^/api/order/user/page/(?P<page>[^/]+)$#',
          ),
          4 => 
          array (
            'method' => 'GET',
            'path' => '/api/order/store/{id}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'getStoreOrders',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.store',
            'pattern' => '#^/api/order/store/(?P<id>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          5 => 
          array (
            'method' => 'GET',
            'path' => '/api/order/store/{id}/status/{status}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'getStoreOrdersByStatus',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.storestatus',
            'pattern' => '#^/api/order/store/(?P<id>[^/]+)/status/(?P<status>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          6 => 
          array (
            'method' => 'GET',
            'path' => '/api/order/track/{code}',
            'controller' => 'App\\Http\\Controllers\\Api\\OrderController',
            'action' => 'trackOrder',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'user',
                  ),
                ),
              ),
            ),
            'name' => 'api.order.track',
            'pattern' => '#^/api/order/track/(?P<code>[^/]+)$#',
          ),
        ),
        'product' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/product/{category}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'findByCategory',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'page' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'total' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'view' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.category',
            'pattern' => '#^/api/product/(?P<category>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/api/product/store/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'findByStore',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'page' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'total' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'view' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.store',
            'pattern' => '#^/api/product/store/(?P<id>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/api/product/store/{id}/category/{category}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'findByStoreCategory',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'page' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'total' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'view' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.storecategory',
            'pattern' => '#^/api/product/store/(?P<id>[^/]+)/category/(?P<category>[^/]+)$#',
          ),
          3 => 
          array (
            'method' => 'GET',
            'path' => '/api/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'findOne',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'api.product.one',
            'pattern' => '#^/api/product/(?P<id>[^/]+)$#',
          ),
          4 => 
          array (
            'method' => 'GET',
            'path' => '/api/product/search/{query}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'findBySearch',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'page' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'total' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'view' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.search',
            'pattern' => '#^/api/product/search/(?P<query>[^/]+)$#',
          ),
          5 => 
          array (
            'method' => 'GET',
            'path' => '/api/product/color/{color}',
            'controller' => 'App\\Http\\Controllers\\Api\\ProductController',
            'action' => 'findByColor',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'page' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'total' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                    'view' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.product.color',
            'pattern' => '#^/api/product/color/(?P<color>[^/]+)$#',
          ),
        ),
        'store' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/store/{id}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'findOne',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.findone',
            'pattern' => '#^/api/store/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/api/store/status/{status}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'findByStatus',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.findstatus',
            'pattern' => '#^/api/store/status/(?P<status>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/api/store/user/{id}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'findByUser',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.finduser',
            'pattern' => '#^/api/store/user/(?P<id>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          3 => 
          array (
            'method' => 'GET',
            'path' => '/api/store/{id}/coupon/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'findCouponsByStore',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.findcoupon',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/coupon/page/(?P<page>[^/]+)$#',
          ),
          4 => 
          array (
            'method' => 'GET',
            'path' => '/api/store/{id}/coupon/status/{status}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'findCouponsByStoreAndStatus',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
            ),
            'name' => 'api.store.findcouponstatus',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/coupon/status/(?P<status>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          5 => 
          array (
            'method' => 'GET',
            'path' => '/api/store/{id}/customer',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'findStoreCustomers',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\ValidationMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'rules' => 
                  array (
                    'type' => 
                    array (
                      0 => 'required',
                      1 => 'string',
                    ),
                    'page' => 
                    array (
                      0 => 'required',
                      1 => 'number',
                    ),
                  ),
                ),
              ),
            ),
            'name' => 'api.store.findcustomer',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/customer$#',
          ),
          6 => 
          array (
            'method' => 'GET',
            'path' => '/api/store/{id}/coupon/{code}',
            'controller' => 'App\\Http\\Controllers\\Api\\StoreController',
            'action' => 'findCoupon',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'api.store.findonecoupon',
            'pattern' => '#^/api/store/(?P<id>[^/]+)/coupon/(?P<code>[^/]+)$#',
          ),
        ),
        'user' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/api/user/role/{role}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Api\\UserController',
            'action' => 'fetch',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.user.fetch',
            'pattern' => '#^/api/user/role/(?P<role>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/api/user/{id}/doc',
            'controller' => 'App\\Http\\Controllers\\Api\\UserController',
            'action' => 'doc',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'api',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\JwtMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
            ),
            'name' => 'api.user.doc',
            'pattern' => '#^/api/user/(?P<id>[^/]+)/doc$#',
          ),
        ),
      ),
    ),
  ),
  'web' => 
  array (
    'static' => 
    array (
      'GET' => 
      array (
        '/auth/user/login' => 
        array (
          'method' => 'GET',
          'path' => '/auth/user/login',
          'controller' => 'App\\Http\\Controllers\\Web\\AuthController',
          'action' => 'userLogin',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 10,
                'anonLimit' => 5,
              ),
            ),
          ),
          'name' => 'auth.user.login',
          'pattern' => NULL,
        ),
        '/auth/user/register' => 
        array (
          'method' => 'GET',
          'path' => '/auth/user/register',
          'controller' => 'App\\Http\\Controllers\\Web\\AuthController',
          'action' => 'userRegister',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 10,
                'anonLimit' => 5,
              ),
            ),
          ),
          'name' => 'auth.user.register',
          'pattern' => NULL,
        ),
        '/auth/admin/login' => 
        array (
          'method' => 'GET',
          'path' => '/auth/admin/login',
          'controller' => 'App\\Http\\Controllers\\Web\\AuthController',
          'action' => 'adminLogin',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 10,
                'anonLimit' => 5,
              ),
            ),
          ),
          'name' => 'auth.admin.login',
          'pattern' => NULL,
        ),
        '/auth/admin/verify' => 
        array (
          'method' => 'GET',
          'path' => '/auth/admin/verify',
          'controller' => 'App\\Http\\Controllers\\Web\\AuthController',
          'action' => 'adminVerify',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 10,
                'anonLimit' => 5,
              ),
            ),
          ),
          'name' => 'auth.admin.verify',
          'pattern' => NULL,
        ),
        '/auth/admin/update' => 
        array (
          'method' => 'GET',
          'path' => '/auth/admin/update',
          'controller' => 'App\\Http\\Controllers\\Web\\AuthController',
          'action' => 'adminUpdate',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 10,
                'anonLimit' => 5,
              ),
            ),
          ),
          'name' => 'auth.admin.update',
          'pattern' => NULL,
        ),
        '/admin/dashboard' => 
        array (
          'method' => 'GET',
          'path' => '/admin/dashboard',
          'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
          'action' => 'dashboard',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.admin.dashboard',
          'pattern' => NULL,
        ),
        '/admin/mail/compose' => 
        array (
          'method' => 'GET',
          'path' => '/admin/mail/compose',
          'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
          'action' => 'mailCompose',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.admin.mail.compose',
          'pattern' => NULL,
        ),
        '/admin/notification' => 
        array (
          'method' => 'GET',
          'path' => '/admin/notification',
          'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
          'action' => 'notification',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.admin.notification',
          'pattern' => NULL,
        ),
        '/admin/profile' => 
        array (
          'method' => 'GET',
          'path' => '/admin/profile',
          'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
          'action' => 'profile',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'admin',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.admin.profile',
          'pattern' => NULL,
        ),
        '/vendor/dashboard' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/dashboard',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'dashboard',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.dashboard',
          'pattern' => NULL,
        ),
        '/vendor/mail/compose' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/mail/compose',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'mailCompose',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.mail.compose',
          'pattern' => NULL,
        ),
        '/vendor/notification' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/notification',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'notification',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.notification',
          'pattern' => NULL,
        ),
        '/vendor/profile' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/profile',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'profile',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.profile',
          'pattern' => NULL,
        ),
        '/vendor/store/create' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/store/create',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'storeCreate',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.store.create',
          'pattern' => NULL,
        ),
        '/vendor/stores' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/stores',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'storeList',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.store.list',
          'pattern' => NULL,
        ),
        '/vendor/wallet' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/wallet',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'wallet',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.wallet',
          'pattern' => NULL,
        ),
        '/vendor/payouts' => 
        array (
          'method' => 'GET',
          'path' => '/vendor/payouts',
          'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
          'action' => 'withdrawal',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RoleMiddleware',
              1 => 'handle',
              2 => 
              array (
                'role' => 
                array (
                  0 => 'vendor',
                ),
              ),
            ),
            1 => 
            array (
              0 => 'App\\Http\\Middlewares\\AuthMiddleware',
              1 => 'handle',
            ),
            2 => 
            array (
              0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
              1 => 'handle',
            ),
            3 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.vendor.withdrawal',
          'pattern' => NULL,
        ),
        '/' => 
        array (
          'method' => 'GET',
          'path' => '/',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'home',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.home',
          'pattern' => NULL,
        ),
        '/about' => 
        array (
          'method' => 'GET',
          'path' => '/about',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'about',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.about',
          'pattern' => NULL,
        ),
        '/brands' => 
        array (
          'method' => 'GET',
          'path' => '/brands',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'brands',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.brands',
          'pattern' => NULL,
        ),
        '/cart' => 
        array (
          'method' => 'GET',
          'path' => '/cart',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'cart',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.cart',
          'pattern' => NULL,
        ),
        '/contact' => 
        array (
          'method' => 'GET',
          'path' => '/contact',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'contact',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.contact',
          'pattern' => NULL,
        ),
        '/delivery' => 
        array (
          'method' => 'GET',
          'path' => '/delivery',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'delivery',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.delivery',
          'pattern' => NULL,
        ),
        '/faq' => 
        array (
          'method' => 'GET',
          'path' => '/faq',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'faq',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.faq',
          'pattern' => NULL,
        ),
        '/orders' => 
        array (
          'method' => 'GET',
          'path' => '/orders',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'orders',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.orders',
          'pattern' => NULL,
        ),
        '/privacy' => 
        array (
          'method' => 'GET',
          'path' => '/privacy',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'privacy',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.privacy',
          'pattern' => NULL,
        ),
        '/profile' => 
        array (
          'method' => 'GET',
          'path' => '/profile',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'profile',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.profile',
          'pattern' => NULL,
        ),
        '/returns' => 
        array (
          'method' => 'GET',
          'path' => '/returns',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'returns',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.returns',
          'pattern' => NULL,
        ),
        '/support' => 
        array (
          'method' => 'GET',
          'path' => '/support',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'support',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.support',
          'pattern' => NULL,
        ),
        '/terms' => 
        array (
          'method' => 'GET',
          'path' => '/terms',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'terms',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.terms',
          'pattern' => NULL,
        ),
        '/testimonials' => 
        array (
          'method' => 'GET',
          'path' => '/testimonials',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'testimonials',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.testimonials',
          'pattern' => NULL,
        ),
        '/track' => 
        array (
          'method' => 'GET',
          'path' => '/track',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'track',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.track',
          'pattern' => NULL,
        ),
        '/wallet' => 
        array (
          'method' => 'GET',
          'path' => '/wallet',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'wallet',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.wallet',
          'pattern' => NULL,
        ),
        '/warranty' => 
        array (
          'method' => 'GET',
          'path' => '/warranty',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'warranty',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.warranty',
          'pattern' => NULL,
        ),
        '/wishlist' => 
        array (
          'method' => 'GET',
          'path' => '/wishlist',
          'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
          'action' => 'wishlist',
          'middlewares' => 
          array (
            0 => 
            array (
              0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
              1 => 'handle',
              2 => 
              array (
                'scope' => 'web',
                'userLimit' => 60,
                'anonLimit' => 20,
              ),
            ),
          ),
          'name' => 'web.public.wishlist',
          'pattern' => NULL,
        ),
      ),
    ),
    'dynamic' => 
    array (
      'GET' => 
      array (
        'admin' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/admin/mail/{id}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'mailRead',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.mail.read',
            'pattern' => '#^/admin/mail/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/admin/mail/outbox/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'mailSent',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.mail.sent',
            'pattern' => '#^/admin/mail/outbox/page/(?P<page>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/admin/mail/inbox/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'mailBox',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.mailbox',
            'pattern' => '#^/admin/mail/inbox/page/(?P<page>[^/]+)$#',
          ),
          3 => 
          array (
            'method' => 'GET',
            'path' => '/admin/order/status/{status}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'orderList',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.order.list',
            'pattern' => '#^/admin/order/status/(?P<status>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          4 => 
          array (
            'method' => 'GET',
            'path' => '/admin/order/{id}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'orderView',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.order.view',
            'pattern' => '#^/admin/order/(?P<id>[^/]+)$#',
          ),
          5 => 
          array (
            'method' => 'GET',
            'path' => '/admin/payouts/status/{status}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'payouts',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.payouts',
            'pattern' => '#^/admin/payouts/status/(?P<status>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          6 => 
          array (
            'method' => 'GET',
            'path' => '/admin/products/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'productList',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.product.list',
            'pattern' => '#^/admin/products/page/(?P<page>[^/]+)$#',
          ),
          7 => 
          array (
            'method' => 'GET',
            'path' => '/admin/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'productView',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.product.view',
            'pattern' => '#^/admin/product/(?P<id>[^/]+)$#',
          ),
          8 => 
          array (
            'method' => 'GET',
            'path' => '/admin/stores/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'storeList',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.store.list',
            'pattern' => '#^/admin/stores/page/(?P<page>[^/]+)$#',
          ),
          9 => 
          array (
            'method' => 'GET',
            'path' => '/admin/users/role/{role}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\AdminController',
            'action' => 'users',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'admin',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.admin.users',
            'pattern' => '#^/admin/users/role/(?P<role>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
        ),
        'vendor' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/vendor/mail/{id}',
            'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
            'action' => 'mailRead',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.vendor.mail.read',
            'pattern' => '#^/vendor/mail/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/vendor/mail/outbox/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
            'action' => 'mailSent',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.vendor.mail.sent',
            'pattern' => '#^/vendor/mail/outbox/page/(?P<page>[^/]+)$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/vendor/mail/inbox/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\VendorController',
            'action' => 'mailBox',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.vendor.mailbox',
            'pattern' => '#^/vendor/mail/inbox/page/(?P<page>[^/]+)$#',
          ),
        ),
        'store' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/dashboard',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'dashboard',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.dashboard',
            'pattern' => '#^/store/(?P<id>[^/]+)/dashboard$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/settings',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'settings',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.settings',
            'pattern' => '#^/store/(?P<id>[^/]+)/settings$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/product/create',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'productCreate',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.product.create',
            'pattern' => '#^/store/(?P<id>[^/]+)/product/create$#',
          ),
          3 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/products/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'productList',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.product.list',
            'pattern' => '#^/store/(?P<id>[^/]+)/products/page/(?P<page>[^/]+)$#',
          ),
          4 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/product/{pid}/view',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'productView',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.product.view',
            'pattern' => '#^/store/(?P<id>[^/]+)/product/(?P<pid>[^/]+)/view$#',
          ),
          5 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/orders/status/{status}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'orderList',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.orders',
            'pattern' => '#^/store/(?P<id>[^/]+)/orders/status/(?P<status>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          6 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/customers/type/{type}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'customers',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.customers',
            'pattern' => '#^/store/(?P<id>[^/]+)/customers/type/(?P<type>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          7 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/coupon/create',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'couponCreate',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.coupon.create',
            'pattern' => '#^/store/(?P<id>[^/]+)/coupon/create$#',
          ),
          8 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/coupons/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\StoreController',
            'action' => 'couponList',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RoleMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'role' => 
                  array (
                    0 => 'vendor',
                  ),
                ),
              ),
              1 => 
              array (
                0 => 'App\\Http\\Middlewares\\AuthMiddleware',
                1 => 'handle',
              ),
              2 => 
              array (
                0 => 'App\\Http\\Middlewares\\CsrfMiddleware',
                1 => 'handle',
              ),
              3 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.store.coupon.list',
            'pattern' => '#^/store/(?P<id>[^/]+)/coupons/page/(?P<page>[^/]+)$#',
          ),
          9 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'store',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.store.view',
            'pattern' => '#^/store/(?P<id>[^/]+)$#',
          ),
          10 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'storePaginated',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.store.view',
            'pattern' => '#^/store/(?P<id>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
          11 => 
          array (
            'method' => 'GET',
            'path' => '/store/{id}/{filter}/{value}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'productStore',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.product.store',
            'pattern' => '#^/store/(?P<id>[^/]+)/(?P<filter>[^/]+)/(?P<value>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
        ),
        'page' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'homePaginated',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.home',
            'pattern' => '#^/page/(?P<page>[^/]+)$#',
          ),
        ),
        'brands' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/brands/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'brandsPaginated',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.brands',
            'pattern' => '#^/brands/page/(?P<page>[^/]+)$#',
          ),
        ),
        'order' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/order/{id}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'orderView',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.order.view',
            'pattern' => '#^/order/(?P<id>[^/]+)$#',
          ),
        ),
        'orders' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/orders/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'ordersPaginated',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.orders',
            'pattern' => '#^/orders/page/(?P<page>[^/]+)$#',
          ),
        ),
        'product' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/product/{id}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'productView',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.product.view',
            'pattern' => '#^/product/(?P<id>[^/]+)$#',
          ),
          1 => 
          array (
            'method' => 'GET',
            'path' => '/product/{search}/search',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'productSearch',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.product.search',
            'pattern' => '#^/product/(?P<search>[^/]+)/search$#',
          ),
          2 => 
          array (
            'method' => 'GET',
            'path' => '/product/{search}/search/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'productSearchPaginated',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.product.search',
            'pattern' => '#^/product/(?P<search>[^/]+)/search/page/(?P<page>[^/]+)$#',
          ),
        ),
        'products' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/products/{filter}/{value}/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'productList',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.product.list',
            'pattern' => '#^/products/(?P<filter>[^/]+)/(?P<value>[^/]+)/page/(?P<page>[^/]+)$#',
          ),
        ),
        'wishlist' => 
        array (
          0 => 
          array (
            'method' => 'GET',
            'path' => '/wishlist/page/{page}',
            'controller' => 'App\\Http\\Controllers\\Web\\HomeController',
            'action' => 'wishlistPaginated',
            'middlewares' => 
            array (
              0 => 
              array (
                0 => 'App\\Http\\Middlewares\\RateLimitMiddleware',
                1 => 'handle',
                2 => 
                array (
                  'scope' => 'web',
                  'userLimit' => 60,
                  'anonLimit' => 20,
                ),
              ),
            ),
            'name' => 'web.public.wishlist',
            'pattern' => '#^/wishlist/page/(?P<page>[^/]+)$#',
          ),
        ),
      ),
    ),
  ),
);
