<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Serializer\Exception\NotFoundEntityException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/product', name: 'product_', methods: ['GET'])]
class ProductController extends AbstractController
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    #[Route('/', name: 'get_all', methods: ['GET'])]
    public function getAll(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(criteria: [], orderBy: ['id' => 'ASC']);

        return $this->json(data: $products, context: [AbstractNormalizer::GROUPS => Product::GROUP_GET]);
    }

    #[Route('/total-count', name: 'get_total_count', methods: ['GET'])]
    public function getTotalCount(ProductRepository $productRepository): Response
    {
        return $this->json(['sum' => $productRepository->getTotalCount()]);
    }

    #[Route('/{id}', name: 'get_one', methods: ['GET'])]
    public function getOne(Product $product): Response
    {
        return $this->json(data: $product, context: [AbstractNormalizer::GROUPS => Product::GROUP_GET]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $content = $request->getContent();
        try {
            $product = $this->serializer->deserialize(
                data: $content,
                type: Product::class,
                format: JsonEncoder::FORMAT,
                context: [AbstractNormalizer::GROUPS => Product::GROUP_SET],
            );
        } catch (NotFoundEntityException $e) {
            return $this->json(data: ['message' => $e->getMessage()], status: Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($product);
        if ($errors->count()) {
            return $this->json(data: ['errors' => $errors], status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($product);
        $em->flush();

        return $this->json(
            data: $product,
            status: Response::HTTP_CREATED,
            context: [AbstractNormalizer::GROUPS => Product::GROUP_SET],
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Product $product,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ): Response {
        $content = $request->getContent();
        try{
            $this->serializer->deserialize($content, Product::class, JsonEncoder::FORMAT, [
                AbstractNormalizer::GROUPS => Product::GROUP_SET,
                AbstractNormalizer::OBJECT_TO_POPULATE => $product,
            ]);
        } catch (NotFoundEntityException $e) {
            return $this->json(data: ['message' => $e->getMessage()], status: Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($product);
        if ($errors->count()) {
            return $this->json(data: ['errors' => $errors], status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
