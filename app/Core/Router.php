<?php
namespace App\Core;

class Router
{
    private $routes = [];

    public function get($path, $action, $middleware = [])
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post($path, $action, $middleware = [])
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put($path, $action, $middleware = [])
    {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function delete($path, $action, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    private function addRoute($method, $path, $action, $middleware)
    {
        // Convert {param} placeholders to regex named groups
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'pattern'    => $pattern,
            'action'     => $action,
            'middleware'  => $middleware,
        ];
    }

    public function dispatch($url, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $url, $matches)) {
                if (in_array($method, ['POST', 'PUT', 'DELETE'], true) && !Csrf::verify(Csrf::fromRequest())) {
                    $this->csrfFailed($url);
                    return;
                }

                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    if ($mw === 'auth' && !Auth::check()) {
                        header('Location: ' . BASE_PATH . '/login');
                        exit;
                    }
                    // Role-based middleware: 'role:admin,agent,...'
                    if (str_starts_with($mw, 'role:')) {
                        $allowedRoles = explode(',', substr($mw, 5));
                        if (!Auth::hasRole($allowedRoles)) {
                            http_response_code(403);
                            if (file_exists(ROOT_DIR . '/app/Views/errors/403.php')) {
                                require ROOT_DIR . '/app/Views/errors/403.php';
                            } else {
                                echo '<h1>403 - Accès refusé</h1><p>Vous n\'avez pas les permissions nécessaires pour accéder à cette page.</p><a href="' . BASE_PATH . '/admin">Retour au tableau de bord</a>';
                            }
                            exit;
                        }
                    }
                }

                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Parse action "Controller@method"
                list($controllerName, $methodName) = explode('@', $route['action']);
                $controllerClass = 'App\\Controllers\\' . $controllerName;

                if (!class_exists($controllerClass)) {
                    $this->notFound("Controller {$controllerClass} not found");
                    return;
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $methodName)) {
                    $this->notFound("Method {$methodName} not found in {$controllerClass}");
                    return;
                }

                call_user_func_array([$controller, $methodName], array_values($params));
                return;
            }
        }

        $this->notFound();
    }

    private function csrfFailed($url)
    {
        http_response_code(419);
        if (strpos($url, '/api/') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Session expirée, veuillez réessayer.']);
            return;
        }

        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Votre session a expiré. Veuillez réessayer.'];
        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_PATH . '/');
        header('Location: ' . $referer);
    }

    private function notFound($message = 'Page non trouvée')
    {
        http_response_code(404);
        if (file_exists(ROOT_DIR . '/app/Views/errors/404.php')) {
            require ROOT_DIR . '/app/Views/errors/404.php';
        } else {
            echo '<h1>404</h1><p>' . htmlspecialchars($message) . '</p>';
        }
    }
}
