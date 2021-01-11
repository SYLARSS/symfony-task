<?php
/**
 * Created by PhpStorm.
 * User: SYLAR
 * Date: 09-Jan-21
 * Time: 15:23
 */

namespace App\Tests;


use App\Entity\Booking;
use App\Entity\Retailer;
use App\Entity\User;

class BookingControllerTest extends BaseCase
{

    protected $token;
    protected $retailerRepo;
    protected $entityManager;
    protected $userRepo;
    protected $bookingRepo;


    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->retailerRepo = $this->entityManager->getRepository(Retailer::class);
        $this->userRepo = $this->entityManager->getRepository(User::class);
        $this->bookingRepo = $this->entityManager->getRepository(Booking::class);
        $this->token = $this->getToken();
    }

    public function testListBookings()
    {

        $response = $this->_execute('get', 'http://localhost:8000/api/bookings', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertIsArray($response);
    }

    public function testShowRetailer()
    {
        $booking = $this->bookingRepo->findBy([], [], 1);
        if (!empty($booking)) {
            $booking = $booking[0];
        }
        $response = $this->_execute('get', 'http://localhost:8000/api/bookings/' . $booking->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);
        $this->assertIsArray($response);
    }

    public function testBookingCreate()
    {
        $retailer = $this->retailerRepo->findBy([], [], 1);
        $retailer = $retailer[0];

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                'retailerId' => $retailer->getId(),
                'orderedStartTime' => '2021-01-01',
                'orderedEndTime' => '2021-01-30',
                'recordedStartTime' => '2021-01-01',
                'recordedStartTime' => '2021-01-30',
                'state' => 3,
                'canceled' => '2021-01-20'
            ]),
        ];
        $response = $this->_execute('post', 'http://localhost:8000/api/bookings', $options);
        $this->assertIsArray($response);
    }

    public function testUpdateBooking()
    {
        $booking = $this->bookingRepo->findBy([], [], 1);
        $booking = $booking[0];
        $oldValue = $booking->getOrderedStartTime();
        $newValue = (new \DateTime($booking->getOrderedStartTime()->format('Y-m-d')))->add(new \DateInterval('P1D'));
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                'orderedStartTime' => $newValue->format('Y-m-d'),
            ]),
        ];
        $response = $this->_execute('put', 'http://localhost:8000/api/bookings/' . $booking->getId(), $options);

        $this->assertIsArray($response);
        $this->assertEquals($newValue->format('Y-m-d'), (new \DateTime($response[0]['orderedStartTime']))->format('Y-m-d'));
        $this->assertNotEquals($oldValue->format('Y-m-d'), (new \DateTime($response[0]['orderedStartTime']))->format('Y-m-d'));
    }
}