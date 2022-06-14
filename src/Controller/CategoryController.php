<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_get_all', methods: ['GET'])]
    public function getAll(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(criteria: [], orderBy: ['id' => 'ASC']);

        return $this->json(data: $categories, context: [AbstractNormalizer::GROUPS => Category::GROUP_GET]);
    }
}
