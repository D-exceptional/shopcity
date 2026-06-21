<?php
namespace App\Core;

use App\Exceptions\MiddlewareException;
use App\Exceptions\RouteNotFoundException;

class Router
{
    protected Container $container;
    protected array $routes = [];
    protected string $groupPrefix = '';
    protected array $groupMiddlewares = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register a route
     */
    public function add(string $method, string $path, string $controller, string $action, array $middlewares = [])
    {
        $fullPath = rtrim($this->groupPrefix . '/' . ltrim($path, '/'), '/');
        $middlewares = array_merge($this->groupMiddlewares, $middlewares);

        // Precompute regex pattern for dynamic parameters
        $pattern = preg_replace('#:([\w]+)#', '(?P<$1>[^/]+)', $fullPath);
        $pattern = "#^" . $pattern . "$#";

        $this->routes[] = [
            'method'      => strtoupper($method),
            'path'        => $fullPath ?: '/',
            'pattern'     => $pattern,
            'controller'  => $controller,
            'action'      => $action,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Route group (for cleaner API design)
     */
    public function group(string $prefix, callable $callback, array $middlewares = [])
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddlewares = $this->groupMiddlewares;

        $this->groupPrefix = rtrim($previousPrefix . '/' . trim($prefix, '/'), '/');
        $this->groupMiddlewares = array_merge($previousMiddlewares, $middlewares);

        $callback($this); // Pass router instance

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddlewares = $previousMiddlewares;
    }

    /**
     * Shorthand REST methods
     */
    public function get(string $path, array $handler, array $middlewares = [])
    {
        $this->add('GET', $path, $handler[0], $handler[1], $middlewares);
    }

    public function post(string $path, array $handler, array $middlewares = [])
    {
        $this->add('POST', $path, $handler[0], $handler[1], $middlewares);
    }

    public function put(string $path, array $handler, array $middlewares = [])
    {
        $this->add('PUT', $path, $handler[0], $handler[1], $middlewares);
    }

    public function delete(string $path, array $handler, array $middlewares = [])
    {
        $this->add('DELETE', $path, $handler[0], $handler[1], $middlewares);
    }

    /**
     * Dispatch request
    */
    public function dispatch(string $uri, string $method)
    {
        foreach ($this->routes as $route) {

            $pattern = $route['pattern'] ?? null;
            $path    = $route['path'] ?? null;

            // Check method first
            if ($route['method'] !== $method) {
                continue;
            }

            // Check URL match: either exact path or regex pattern
            $matches = [];
            $isMatch = ($uri === $path) || ($pattern && preg_match($pattern, $uri, $matches));

            if (!$isMatch) {
                continue;
            }

            // Extract named params if regex matched
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $payload = $this->parseRequestPayload($method);
            $payload = array_merge($payload, $params);

            // Run middlewares
            foreach ($route['middlewares'] as [$class, $method]) {
                $middleware = $this->container->get($class);
                $result     = $middleware->{$method}();

                if (($result['ok'] ?? false) === false) {
                    // Use MiddlewareException to handle middleware denials
                    throw new MiddlewareException(
                        $result['message'] ?? 'Blocked by middleware',
                        $result['status']  ?? 403,
                        $result['action']  ?? 'json',
                        $result['redirect'] ?? null
                    );
                }
            }

            // Instantiate controller
            $controllerClass = $route['controller'];
            if (!class_exists($controllerClass)) {
                $this->logError("Controller $controllerClass not found");
                throw new \Exception("Controller $controllerClass not found");
            }

            // Resolve controller from container
            $controller = $this->container->get($controllerClass);
            $action = $route['action'];

            if (!method_exists($controller, $action)) {
                $this->logError("Action $action not found in $controllerClass");
                throw new \Exception("Action $action not found in $controllerClass");
            }

            // Execute controller action
            try {
                $controller->{$action}($payload);
                return; // STOP here — prevents any further fallback to 404
            } catch (\Throwable $e) {
                $this->logError("Uncaught Exception in $controllerClass::$action — " . $e->getMessage());
                throw new \Exception("Controller execution error: " . $e->getMessage());
            }
        }

        // No route matched: send 404 and exit
        $this->logError("Route not found: $uri");
        throw new RouteNotFoundException("Route not found: $uri");
    }

    /**
     * Parse request payload
     */
    protected function parseRequestPayload(string $method): array
    {
        if ($method === 'GET') {
            parse_str($_SERVER['QUERY_STRING'] ?? '', $payload);
            return $payload;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $payload = [];

        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->response->error('Malformed JSON: ' . json_last_error_msg(), 400);
            }
            $payload = $decoded ?? [];
        } elseif (stripos($contentType, 'multipart/form-data') !== false) {
            $payload = $_POST;
            if (!empty($_FILES)) {
                $payload['_files'] = $_FILES;
            }
        } elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
            $payload = $_POST;
        } else {
            $raw = file_get_contents('php://input');
            $payload['raw'] = $raw;
        }

        return $payload;
    }

    /**
     * Parse request payload
    */
    private function logError(string $message): void
    {
        $logFile = dirname(__DIR__, 2) . '/storage/logs/router-error.log';
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[{$timestamp}] {$message}\n";
        error_log($entry, 3, $logFile);
    }
}
