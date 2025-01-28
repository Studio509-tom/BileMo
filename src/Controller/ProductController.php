<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

final class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products',methods:['GET'])]  
    /**
     * Récupérer tout les produits
     * @param  ProductRepository $productRepository
     * @param  SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getProducts(ProductRepository $productRepository ,SerializerInterface $serializer): JsonResponse
    {
        // Récupération de tout les produits
        $products = $productRepository->findAll();
        // Retour JSON serializer
        $context = SerializationContext::create()->setGroups(['getProducts']);
        $jsonProducts = $serializer->serialize($products, 'json' , $context);
        return new JsonResponse($jsonProducts, Response::HTTP_OK , [], true);
    }

    #[Route('/api/product/{id}', name: 'product_details',methods:['GET'])]    
    /**
     * Récupérer le details d'un produit
     * @param  Product $product
     * @param  SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getProduct(Product $product,SerializerInterface $serializer): JsonResponse
    {
        // Retour du product
        $context = SerializationContext::create()->setGroups(['getProducts']);
        $jsonProduct = $serializer->serialize($product, 'json' , $context);

        return new JsonResponse($jsonProduct, Response::HTTP_OK , [], true);

    }
}
