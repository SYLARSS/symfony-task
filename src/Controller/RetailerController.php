<?php

namespace App\Controller;

use App\Entity\Retailer;
use App\Repository\RetailerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class RetailerController extends AbstractController
{
    /**
     * @Route("/retailers", name="retailers", methods={"GET"})
     */
    public function list(RetailerRepository $retailerRepository): Response
    {
        return $this->json($retailerRepository->createQueryBuilder('r')->getQuery()->getArrayResult());
    }

    /**
     * @Route("/retailers/{id}", name="retailer_show", methods={"GET"})
     */
    public function show(int $id, RetailerRepository $retailerRepository): Response
    {
        $retailer = $retailerRepository->createQueryBuilder('r')
            ->where('r.id = :retailerId')
            ->setParameter('retailerId', $id)
            ->getQuery()
            ->getArrayResult();
        return $this->json($retailer);
    }

    /**
     * @Route("/retailers/{id}", name="retailer_update", methods={"PUT"})
     */
    public function update($id, RetailerRepository $retailerRepository, Request $request, UserRepository $userRepository): Response
    {
        $retailer = $retailerRepository->find($id);
        if (!$retailer) {
            return $this->json('Retailer Not Found!');
        }
        $requestData = json_decode($request->getContent(), true);
        if (!empty($requestData['dealerName'])) {
            $retailer->setDealerName($requestData['dealerName']);
        }
        if (!empty($requestData['dealerNumber'])) {
            $retailer->setDealerNumber($requestData['dealerNumber']);
        }
        if (!empty($requestData['userId'])) {
            $user = $userRepository->find($requestData['userId']);
            $retailer->setUser($user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($retailer);
        $em->flush();
        $retailers = $retailerRepository->createQueryBuilder('r')
            ->innerJoin('App:User', 'u', "with", 'r.userId = u.id')
            ->where('r.id = :retailerId')
            ->setParameter('retailerId', $retailer->getId())
            ->getQuery()
            ->getArrayResult();
        return $this->json($retailers);
    }

    /**
     * @Route("/retailers", name="retailer_create", methods={"POST"})
     */
    public function create(RetailerRepository $retailerRepository, Request $request, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $retailer = new Retailer();

        $requestData = json_decode($request->getContent(), true);
        $user = $userRepository->find($requestData['userId']);

        if (!empty($requestData['dealerName'])) {
            $retailer->setDealerName($requestData['dealerName']);
        }
        if (!empty($requestData['dealerNumber'])) {
            $retailer->setDealerNumber($requestData['dealerNumber']);
        }
        $user->addRetailer($retailer);
        $em = $this->getDoctrine()->getManager();
        $em->persist($retailer);
        $em->persist($user);
        $em->flush();
        $retailers = $retailerRepository->createQueryBuilder('r')
            ->innerJoin('App:User', 'u', "with", 'r.userId = u.id')
            ->where('r.id = :retailerId')
            ->setParameter('retailerId', $retailer->getId())
            ->getQuery()
            ->getArrayResult();
        return $this->json($retailers);
    }
}
