<?php
class Router {
    private $routes = [];
    
    public function add($route, $controller, $method) {
        $this->routes[] = [
            'route' => $route,
            'controller' => $controller,
            'method' => $method
        ];
    }
    
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace('/app-estacion', '', $uri);
        
        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['route']);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                
                $controller = new $route['controller']();
                call_user_func_array([$controller, $route['method']], $matches);
                return;
            }
        }
        
        // 404
        http_response_code(404);
        echo "Página no encontrada";
    }
}