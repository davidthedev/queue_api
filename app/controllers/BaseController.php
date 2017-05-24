<?php

abstract class BaseController {
    protected $queryString;
    protected $requestMethod;
    protected $db;

    public function __construct($queryString, $requestMethod, $db)
    {
        $this->queryString      = explode('&', $queryString);
        $this->requestMethod    = $requestMethod;
        $this->db               = $db;
    }

    public function json($response)
    {
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
}
