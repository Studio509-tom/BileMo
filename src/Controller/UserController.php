<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
// use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

final class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users', methods: ["GET"])]    
    /**
     * getUserFromToken
     *
     * @param  TokenStorageInterface $tokenStorage
     * @param  CustomerRepository $customerRepository
     * @param  SerializerInterface $serializer
     * @param  UserRepository $userRepository
     * @return JsonResponse
     */
    public function getUserFromToken(TokenStorageInterface $tokenStorage, CustomerRepository $customerRepository, SerializerInterface $serializer, UserRepository $userRepository)
    {
        $token = $tokenStorage->getToken();

        // Vérifiez si le token et l'utilisateur existent
        if ($token && ($user = $token->getUser()) && is_object($user)) {
            // Récupération des ids users correspondant à l'admin
            $usersOfCustomer = $customerRepository->findUsersByCustomerId($user);
            $users = [];
            // Formattage des users
            foreach ($usersOfCustomer as $value) {
                $users[] = $userRepository->findBy(["id" => $value]);
            }
            // Retour Json
            $context = SerializationContext::create()->setGroups(['user_read']);
            $jsonList = $serializer->serialize($users, 'json', $context);
            return new JsonResponse($jsonList, Response::HTTP_OK, [], true);
        }
    }
    #[Route('/api/user/{id}', name: 'user_details', methods: ["GET"])]    
    /**
     * getUserDetails
     *
     * @param  User $user
     * @param  TokenStorageInterface $tokenStorage
     * @param  SerializerInterface $serializer
     * @param  CustomerRepository $customerRepository
     * @param  UserRepository $userRepository
     * @return JsonResponse
     */
    public function getUserDetails(User $user, TokenStorageInterface $tokenStorage, SerializerInterface $serializer, CustomerRepository $customerRepository, UserRepository $userRepository)
    {
        $token = $tokenStorage->getToken();

        // Vérifiez si le token et l'utilisateur existent
        if ($token && ($user_admin = $token->getUser()) && is_object($user_admin)) {
            $is_authorized = $customerRepository->isAccepted($user_admin->getId(), $user->getId());

            if (!is_null($is_authorized)) {
                $user_new = $userRepository->find($user);
                $context = SerializationContext::create()->setGroups(['user_read']);
                $jsonList = $serializer->serialize($user_new, 'json', $context);
                return new JsonResponse($jsonList, Response::HTTP_OK, [], true);
            }else{
                return new JsonResponse( "Vous n'êtes pas autorisé à voir cette utilisateur" ,Response::HTTP_BAD_REQUEST, [], true);

            }
        }
    }
}
