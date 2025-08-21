<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    private $token;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher , JWTTokenManagerInterface $JWTManager )
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->token = $JWTManager;
    }
    public function load(ObjectManager $manager  ): void
    {
        $listAdmin =[];
        for ($i = 0; $i < 20 ; $i++){
            $product = new Product;
            $product->setName('Téléphone-' . $i);
            $product->setDescription("Déscription du téléphone-".$i);
            $manager->persist($product);
        }

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail("tata" . $i."@test.fr");
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $user->setRoles(['ROLE_ADMIN']);
            $user->setToken($this->token->create($user));
            $manager->persist($user);
            $listAdmin[] = $user;
        }

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail("toto" . $i."@test.fr");
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $user->setRoles(['ROLE_USER']);
            $user->setToken($this->token->create($user));
            $referent = $listAdmin[array_rand($listAdmin)];
            $user->setReferent($referent);
            $manager->persist($user);

            $customer = new Customer;
            $customer->setCustomer($referent);
            $customer->setUser($user);
            $manager->persist($customer);
        }
        
        $manager->flush();
    }
}
