<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function list(UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $users = $userRepository->createQueryBuilder('u')->getQuery()->getArrayResult();
        return $this->json($users);
    }

    /**
     * @Route("/users/{id}", name="user", methods={"GET"})
     */
    public function show($id, UserRepository $userRepository, Request $request, SerializerInterface $serializer): Response
    {
        $user = $userRepository->createQueryBuilder('u')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
        return $this->json($user);
    }

    /**
     * @Route("/users/{id}", name="user_update", methods={"PUT"})
     */
    public function update($id, UserRepository $userRepository, Request $request, SerializerInterface $serializer): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json('User Not Found!');
        }
        $requestData = json_decode($request->getContent(), true);
        if (!empty($requestData['birthDay'])) {
            $user->setBirthDate($requestData['birthDay']);
        }
        if (!empty($requestData['firstName'])) {
            $user->setFirstName($requestData['firstName']);
        }
        if (!empty($requestData['lastName'])) {
            $user->setLastName($requestData['lastName']);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response($serializer->serialize($user, 'json'));
    }
}
