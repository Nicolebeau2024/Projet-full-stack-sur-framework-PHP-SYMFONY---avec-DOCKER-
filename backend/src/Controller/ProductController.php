<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        return $this->json($products);
    }

    #[Route('/product/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }
        return $this->json($product);
    }

    #[Route('/product', name: 'product_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setCreatedAt(new \DateTime());

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product, 201);
    }

    #[Route('/product/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $entityManager->flush();

        return $this->json($product);
    }

    #[Route('/product/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(['message' => 'Product deleted']);
    }
}
