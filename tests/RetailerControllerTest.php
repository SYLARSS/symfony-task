<?php
/**
 * Created by PhpStorm.
 * User: SYLAR
 * Date: 09-Jan-21
 * Time: 15:23
 */

namespace App\Tests;


use App\Entity\Retailer;
use App\Entity\User;

class RetailerControllerTest extends BaseCase
{

    protected $token;
    protected $retailerRepo;
    protected $entityManager;
    protected $userRepo;


    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->retailerRepo = $this->entityManager->getRepository(Retailer::class);
        $this->userRepo = $this->entityManager->getRepository(User::class);
        $this->token = $this->getToken();
    }

    public function testListRetailers()
    {

        $response = $this->_execute('get', 'http://localhost:8000/api/retailers', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertIsArray($response);
    }

    public function testShowRetailer()
    {
        $retailer = $this->retailerRepo->findBy([], [], 1);
        if (!empty($retailer)) {
            $retailer = $retailer[0];
        }
        $response = $this->_execute('get', 'http://localhost:8000/api/retailers/' . $retailer->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertIsArray($response);
    }

    public function testCreateRetailer()
    {
        $user = $this->userRepo->findBy([], [], 1);
        $user = $user[0];

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                'userId' => $user->getId(),
                'dealerName' => 'DealerName',
                'dealerNumber' => '08888888'
            ]),
        ];
        $response = $this->_execute('post', 'http://localhost:8000/api/retailers', $options);
        $this->assertIsArray($response);
    }

    public function testUpdateRetailer()
    {
        $retailer = $this->retailerRepo->findBy([], [], 1);
        $retailer = $retailer[0];
        $value = 'DealerName' . rand(0, 2222);
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                'dealerName' => $value,
            ]),
        ];
        $response = $this->_execute('put', 'http://localhost:8000/api/retailers/' . $retailer->getId(), $options);
        $this->assertIsArray($response);
        $this->assertEquals($value, $response[0]['dealerName']);
    }
}