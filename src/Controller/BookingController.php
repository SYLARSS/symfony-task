<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\RetailerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class BookingController extends AbstractController
{

    /**
     * @Route("/bookings", name="bookings", methods={"GET"})
     */
    public function list(BookingRepository $bookingRepository): Response
    {
        $bookings = $bookingRepository->createQueryBuilder('b')->getQuery()->getArrayResult();
        return $this->json($bookings);
    }

    /**
     * @Route("/bookings/{id}", name="booking_show", methods={"GET"})
     */
    public function show(int $id, BookingRepository $bookingRepository, RetailerRepository $retailerRepository, UserRepository $userRepository): Response
    {
        $booking = $bookingRepository->createQueryBuilder('b')
            ->where('b.id = :bookingId')
            ->setParameter('bookingId', $id)
            ->getQuery()
            ->getArrayResult();
        return $this->json($booking);
    }

    /**
     * @Route("/bookings/{id}", name="bookings_update", methods={"PUT"})
     */
    public function update($id, RetailerRepository $retailerRepository, Request $request, BookingRepository $bookingRepository): Response
    {
        $booking = $bookingRepository->find($id);
        if (!$booking) {
            return $this->json('Booking Not Found!');
        }
        $requestData = json_decode($request->getContent(), true);
        $retailer = $booking->getRetailer();
        $user = $retailer->getUser();
        if (!$user || !$retailer) {
            return $this->json(['error' => '400']);
        }
        if (!empty($requestData['orderedStartTime'])) {
            $booking->setOrderedStartTime($this->createDateTime($requestData['orderedStartTime']));
        }
        if (!empty($requestData['orderedEndTime'])) {
            $booking->setOrderedEndTime($this->createDateTime($requestData['orderEndTime']));
        }
        if (!empty($requestData['recordedStartTime'])) {
            $booking->setRecordedStartTime($this->createDateTime($requestData['recordedStartTime']));
        }
        if (!empty($requestData['recordedEndTime'])) {
            $booking->setRecordedEndTime($this->createDateTime($requestData['recordedEndTime']));
        }
        if (!empty($requestData['state'])) {
            $booking->setState($requestData['state']);
        }
        if (!empty($requestData['canceled'])) {
            $booking->setCanceled($this->createDateTime($requestData['canceled']));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($booking);
        $em->flush();
        $booking = $bookingRepository->createQueryBuilder('b')
            ->where('b.id = :bookingId')
            ->setParameter('bookingId', $booking->getId())
            ->getQuery()
            ->getArrayResult();
        return $this->json($booking);
    }

    /**
     * @Route("/bookings", name="bookings_create", methods={"POST"})
     */
    public function create(RetailerRepository $retailerRepository, Request $request, BookingRepository $bookingRepository, MailerInterface $mailer): Response
    {
        $booking = new Booking();
        $requestData = json_decode($request->getContent(), true);
        $retailer = $retailerRepository->find($requestData['retailerId']);
        $user = $retailer->getUser();
        if (!$user || !$retailer) {
            return $this->json(['error' => '400']);
        }
        if (!empty($requestData['orderStartTime'])) {
            $booking->setOrderedStartTime($this->createDateTime($requestData['orderStartTime']));
        }
        if (!empty($requestData['orderEndTime'])) {
            $booking->setOrderedEndTime($this->createDateTime($requestData['orderEndTime']));
        }
        if (!empty($requestData['recordStartTime'])) {
            $booking->setRecordedStartTime($this->createDateTime($requestData['recordStartTime']));
        }
        if (!empty($requestData['recordEndTime'])) {
            $booking->setRecordedEndTime($this->createDateTime($requestData['recordEndTime']));
        }
        if (!empty($requestData['state'])) {
            $booking->setState($requestData['state']);
        }
        if (!empty($requestData['canceled'])) {
            $booking->setCanceled($this->createDateTime($requestData['canceled']));
        }
        $booking->setRetailer($retailer);
        $booking->setUser($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($booking);
        $em->flush();
        $booking = $bookingRepository->createQueryBuilder('b')
            ->where('b.id = :bookingId')
            ->setParameter('bookingId', $booking->getId())
            ->getQuery()
            ->getArrayResult();

        $email = (new Email())
            ->from($_ENV['EMAIL_FROM'])
            ->to($user->getEmail())
            ->subject('Booking successful')
            ->text(json_encode($booking));
        $mailer->send($email);
        return $this->json($booking);
    }

    private function createDateTime($dateTime)
    {
        try {
            return new \DateTime($dateTime);
        } catch (\Exception $ex) {
            return new \DateTime();
        }
    }
}
