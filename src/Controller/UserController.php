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
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
/**
 * * @OA\Tag(name="Utilisateurs")
 */
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
            // Si l'admin qui fait la requêtes est autorisée à voir l'user 
            if (!is_null($is_authorized)) {
                // Return JsonResponse
                $context = SerializationContext::create()->setGroups(['user_read']);
                $jsonUser = $serializer->serialize($user, 'json', $context);
                return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
            }else{
                return new JsonResponse( "Vous n'êtes pas autorisé à voir cette utilisateur" ,Response::HTTP_BAD_REQUEST, [], true);
            }
        }
    }

    #[Route('/api/user', name: 'add_user', methods: ["POST"])]        
    /**
     * setUser
     *
     * @param  Request $request
     * @param  TokenStorageInterface $tokenStorage
     * @param  SerializerInterface $serializer
     * @param  EntityManagerInterface $em
     * @param  UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function setUser(Request $request,TokenStorageInterface $tokenStorage, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse{
        // Récupération de l'utilisateur dans la requete
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $em->persist($user);
        // Initialisation de la ligne dans customer
        $token = $tokenStorage->getToken();
        $customer = new Customer;
        $customer->setCustomer($token->getUser());
        $customer->setUser($user);
        
        $em->persist($customer);
        // Enregistré en BDD
        $em->flush();

        // Retour Json
        $context = SerializationContext::create()->setGroups(['user_read']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        $location = $urlGenerator->generate('user_details', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/user/{id}', name: 'delete_user', methods: ["DELETE"])]        
      
    /**
     * deleteUser
     *
     * @param  User $user
     * @param  CustomerRepository $customerRepository
     * @param  EntityManagerInterface $em
     * @return JsonResponse
     */
    public function deleteUser(User $user,CustomerRepository $customerRepository, EntityManagerInterface $em): JsonResponse{
        // delete relation with customer   
        
        $customerRepository->deleteCustomerWithUserId($user->getId());
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
