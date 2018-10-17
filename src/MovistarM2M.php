<?php
namespace BionConnection\MovistarM2M;
use Illuminate\Support\ServiceProvider;
use Config;
use Exception;



class MovistarM2M extends ServiceProvider
{

   public function execute($action, $params=[])
	{

		// Initiate

		$params['username'] 	= config('whmcs.username');
		$params['responsetype'] = config('whmcs.responsetype','json');
		$params['action']		= $action;

		$auth_type = config('whmcs.auth_type', 'password');

		switch($auth_type)
		{
			case 'api':
			if(false === Config::has('whmcs.password') || '' === config('whmcs.password'))
				throw new Exception("Please provide api key for authentication");
			$params['accesskey'] = config('whmcs.password');
			break;
			case 'password':
			if(false === Config::has('whmcs.password') || '' === config('whmcs.password'))
				throw new Exception("Please provide username password for authentication");
			$params['password'] 	= md5(config('whmcs.password'));
			break;
		}

		$url = config('whmcs.url');
		// unset url
		unset($params['url']);
		$client = new Client(['defaults' => ['headers' => ['User-Agent' => null]]]);
		try
		{
			$response = $client->post($url, ['form_params' => $params,'timeout' => 1200,'connect_timeout' => 10]);

			try
			{
				return $this->processResponse(json_decode($response->getBody()->getContents(), true));
			}
			catch(ParseException $e)
			{
				return $this->processResponse($response->xml());
			}
		}
		catch(ClientException $e)
		{
			$response = json_decode($e->getResponse()->getBody()->getContents(), true);
			throw new Exception($response['message']);
		}

	}

	public function processResponse($response)
	{
		if(isset($response['result']) && 'error' === $response['result']
			|| isset($response['status']) && 'error' === $response['status'] )
			throw new Exception("WHMCS Response Error : ".$response['message']);
		return json_decode(json_encode($response),'array'===config('whmcs.response', 'object'));
	}

	// using magic method
	public function __call($action, $params)
	{
		return $this->execute($action, $params);
	}
    
}




/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

