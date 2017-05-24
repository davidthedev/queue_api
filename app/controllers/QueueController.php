<?php

require_once 'BaseController.php';

class QueueController extends BaseController {

    private $table = 'queue';

    public function run()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $this->get();
                break;
            case 'POST':
                $this->post();
                break;
            default:
                die('Method not allowed')
                break;
        }
    }

    /**
     * Retrieve all entries queued today
     *
     * Use optional 'type' filter
     *
     * @return void
     */
    public function get()
    {
        $where      = [];
        $response   = [];

        // check if query string exists
        if ($this->queryString) {
            foreach ($this->queryString as $queryString) {
                // check if parameter 'type' exists
                // if it does add to 'where' array
                if (strpos($queryString, 'type') !== false) {
                    $queryString = explode('=', $queryString);
                    $where[] = "type = '$queryString[1]'";
                }
            }
        }

        // filter only today's entries
        $where[] = 'DATE(queuedDate) = ' . 'CURDATE()';

        $where = implode(' AND ', $where);
        $result = $this->db->select($this->table, $where);

        $response = [
            'status'    => 'Success',
            'data'      => $result
        ];

        $this->json($response);
    }

    /**
     * Post endpoint
     *
     * @return void
     */
    public function post()
    {
        $requestParams          = json_decode(file_get_contents('php://input'), true);
        $response               = ['status' => '', 'data' => ''];
        $params                 = ['type', 'service', 'firstName', 'lastName', 'organization'];
        $mandatoryParams        = ['type', 'service'];

        $allowedTypeParams      = ['citizen', 'anonymous'];
        $allowedServiceParams   = ['council tax', 'benetifs', 'rent'];

        foreach ($params as $key => $param) {
            unset($params[$key]);
            $params[$param] = isset($requestParams[$param]) ? trim($requestParams[$param]) : '';
        }

        // check for type
        if ($params['type']) {
            if (in_array(strtolower($params['type']), $allowedTypeParams)) {
                if (empty($params['firstName'])) {
                    $response = [
                        'status'    => 'Error',
                        'data'      => 'First name is required'
                    ];
                    $this->json($response);
                } elseif (empty($params['lastName'])) {
                    $response = [
                        'status'    => 'Error',
                        'data'      => 'Last name is required'
                    ];
                    $this->json($response);
                }
            } else {
                $response = [
                    'status'    => 'Error',
                    'data'      => 'Type is not allowed'
                ];
                $this->json($response);
            }
        } else {
            $response = [
                'status'    => 'Error',
                'data'      => 'Type is required'
            ];
            $this->json($response);
        }

        // check for service
        if ($params['service']) {
            if (!in_array(strtolower($params['service']), $allowedServiceParams)) {
                $response = [
                    'status'    => 'Error',
                    'data'      => 'Service is not allowed'
                ];
                $this->json($response);
            }
        } else {
            $response = [
                'status'    => 'Error',
                'data'      => 'Service is required',
            ];
            $this->json($response);
        }

        $this->db->insert($this->table, $params);

        $response = [
            'status'    => 'Success',
            'data'      => 'Successfully imported',
        ];
        $this->json($response);
    }
}
