<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
final class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products',methods:['GET'])]    
    /**
     * getProducts
     *
     * @param  ProductRepository $productRepository
     * @param  SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getProducts(ProductRepository $productRepository ,SerializerInterface $serializer): JsonResponse
    {
        // Récupération de tout les produits
        $products = $productRepository->findAll();
        // Retour JSON serializer
        $jsonProducts = $serializer->serialize($products, 'json' , [ 'groups' => "getProducts"]);
        return new JsonResponse($jsonProducts, Response::HTTP_OK , [], true);
    }

    #[Route('/api/product/{id}', name: 'product',methods:['GET'])]    
    /**
     * getProduct
     *
     * @param  Product $product
     * @param  SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getProduct(Product $product,SerializerInterface $serializer): JsonResponse
    {
        // Retour du product
        $jsonProduct = $serializer->serialize($product, 'json' , [ 'groups' => "getProducts"]);

        return new JsonResponse($jsonProduct, Response::HTTP_OK , [], true);

    }
}
