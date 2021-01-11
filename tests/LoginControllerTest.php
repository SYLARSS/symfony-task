<?php
/**
 * Created by PhpStorm.
 * User: SYLAR
 * Date: 09-Jan-21
 * Time: 15:23
 */

namespace App\Tests;


use GuzzleHttp\Client;

class loginControllerTest extends BaseCase
{


    public function testBadToken()
    {

        $response = $this->_execute('get', 'http://localhost:8000/api/retailers', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer WRONG'
            ]
        ]);
        $this->assertEquals(401, $response['error']);

    }

    public function testLogin()
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
//        $this->_execute('post', 'http://localhost:8000/auth/register', $options);
        $response = $this->_execute('post', 'http://localhost:8000/api/login_check', $options);
        $this->assertArrayHasKey('token', $response);
    }

    public function testRegister()
    {
        $options = [
            'form_params' => [
                'email' => 'pavelnovakov@gmail.com' . rand(0, 5000),
                'password' => 'passs123',
                'firstName' => 'Pavel'
            ]
        ];
        $response = $this->_execute('post', 'http://localhost:8000/auth/register', $options);
        $this->assertArrayHasKey('user', $response);
    }

}