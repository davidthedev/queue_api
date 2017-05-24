<?php

class App {

    private $controller = '';
    private $method     = '';

    public function __construct($db)
    {
        $requestUri         = $_SERVER['REQUEST_URI'];
        $queryString        = $_SERVER['QUERY_STRING'];
        $requestMethod      = $_SERVER['REQUEST_METHOD'];
        $requestUriParts    = explode('/', $requestUri);

        if ($queryString) {
            $endPoint = end($requestUriParts);
            $endPoint = explode('?', $endPoint);
            $endPoint = reset($endPoint);
        } else {
            $endPoint = end($requestUriParts);
        }

        $controllerName = ucfirst($endPoint) . 'Controller';
        $fileExt = '.php';

        $controller = 'controllers/' . $controllerName;

        if (file_exists(BASE_LOCATION . '/app/' . $controller . $fileExt)) {
            require_once $controller . $fileExt;
            $this->controller = new $controllerName($queryString, $requestMethod, $db);
            $this->controller->run();
        } else {
            echo 'no controller';
        }
    }
}
