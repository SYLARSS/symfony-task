<?php
/**
 * Created by PhpStorm.
 * User: SYLAR
 * Date: 11-Jan-21
 * Time: 10:59
 */

namespace App\Tests;


use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseCase extends KernelTestCase
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->client = new Client();
        parent::__construct($name, $data, $dataName);
    }

    protected function getToken()
    {
        $options = [
            'body' => json_encode([
                'username' => 'pavelnovakov@gmail.com',
                'password' => 'passs123'
            ]),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];
        $response = $this->_execute('post', 'http://localhost:8000/api/login_check', $options);
        return $response['token'];
    }

    protected function _execute($method, $url, $options)
    {
        try {
            $response = $this->client->$method($url, $options);
            $response = json_decode($response->getBody()->getContents(), true);
            return $response;
        } catch (\Exception $exception) {
            $response = [
                'error' => $exception->getCode(),
                'message' => $exception->getMessage()
            ];
        }
        return $response;
    }
}