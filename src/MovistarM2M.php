<?php

namespace BionConnection\MovistarM2M;

use InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Config;

class MovistarM2M {

    const STATUS_ACTIVATION_PENDANT = 'ACTIVATION_PENDANT';
    const STATUS_ACTIVATION_READY = 'ACTIVATION_READY';
    const STATUS_INACTIVE_NEW = 'INACTIVE_NEW';
    const STATUS_DEACTIVATED = "DEACTIVATED";
    const STATUS_RETIRED = "RETIRED";
    const TIMEOUT = 10;

    private $uri_key_pem = '';
    private $uri_ca_pem = '';
    private $api_endpoint = '';
    private $request_successful = false;
    private $last_error = '';
    private $last_response = array();
    private $last_request = array();
    private $response;

    public function __construct() {

        $this->uri_key_pem = config('movistarm2m.uri_key_pem');
        $this->uri_ca_pem = config('movistarm2m.uri_ca_pem');
        $this->response = null;
        $this->api_endpoint = config('movistarm2m.url');
        $this->client = new Client(['http_errors' => false]);
    }

    public function getSims($icc = null, $inactive_new = null) {
        $param = array();
        if (isset($icc)) {
            $param["icc"] = $icc;
        }
        if (isset($inactive_new)) {
            $param["lifeCycleState"] = STATUS_INACTIVE_NEW;
        }


        $this->makeRequest("get", "Inventory/v6/r12/sim", $param);
    }

    private function changeSimStatus($icc, $status) {
        $param = array("lifeCycleState" => $status);
        return $this->makeRequest("put", "Inventory/v6/r12/sim/icc:" . $icc, $param);
    }

    public function changeSimCommercialPlan($icc, $id_commercial_plan) {

        $param = array("commercialGroup" => $id_commercial_plan);
        return $this->makeRequest("put", "Inventory/v6/r12/sim/icc:" . $icc, $param);
    }

    private function changeExpenseLimit($icc, array $expense) {
        $expenseMovistar = array();

        $limites = array();
        foreach ($expense as $key => $value) {
            switch ($key) {
                case "SMS":
                    $limites["smsEnabled"] = true;
                    $limites["smsLimit"] = $value;
                    break;
                case "DATA":
                    $limites["dataEnabled"] = true;
                    $limites["dataLimit"] = $value;
                    break;
                case "VOICE":
                    $limites["voiceEnabled"] = true;
                    $limites["voiceLimit"] = $value;
                    break;
            }
        }

        $expenseMovistar["monthlyConsumptionThreshold"] = $limites;
        return $this->makeRequest("put", "Inventory/v6/r12/sim/icc:" . $icc, $expenseMovistar);
    }

    public function activateSim($icc, $id_commercial_plan = null, array $expense = null) {
        $sim = $this->getSims($icc);
        $respuesta = true;
        if ($sim) { ///evaluo si si esta suspendido
            if (!$this->changeCommercialPlan($icc, $id_commercial_plan)) {
                $respuesta = false;
            }; // cambio el plan comercial
            if (!$this->changeExpenseLimit($icc, $expense)) {
                $respuesta = false;
            }; // cambio los limites
            if ($this->changeSimStatus($icc, STATUS_ACTIVATION_PENDANT)) {
                $respuesta = false;
            }
        }
        return $respuesta;
    }

    public function suspendSim($icc) {

        $this->changeSimStatus($icc, STATUS_DEACTIVATED); // Cambio el sim suspendida
    }

    public function terminateSim($icc) {
        $this->changeSimStatus($icc, STATUS_RETIRED); // Cambio el sim RETIRED
    }

    public function getLocationSim($icc) {

        return $this->makeRequest("get", "Inventory/v6/r12/sim/icc:" . $icc . "/location");
    }

    public function getSimStatusGSM() {

        return $this->makeRequest("get", "Inventory/v6/r12/sim/icc:" . $icc . "/syncDiagnostic/gsm");
    }

    private function makeRequest($http_verb, $method, $args = array(), $timeout = self::TIMEOUT) {
        unset($this->response);
        $url = $this->api_endpoint . '/' . $method;
        switch ($http_verb) {
            case 'post':
                break;
            case 'get':
                $this->response = $this->client->get($url, ['cert' => $this->uri_ca_pem, 'ssl_key' => $this->uri_key_pem, 'query' => $args, 'timeout' => $timeout])->getBody();
                return $this->response;
                break;
            case 'delete':

                break;
            case 'patch':

                break;
            case 'put':
                return $this->client->put($url, ['cert' => $this->uri_ca_pem, 'ssl_key' => $this->uri_key_pem, 'body' => $args, 'timeout' => $timeout])->getBody();

                break;
        }
    }

}
