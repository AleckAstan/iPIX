<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\PicturesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/{slug}", name="category_pics")
     */
    public function category(
        Category $category,
        PicturesRepository $picturesRepository
    ): Response {
        $pictures = $picturesRepository->findAllPictures($category);

        return $this->render('category/oneCategory.html.twig', [
            'category' => $category,
            'pictures' => $pictures,
        ]);
    }
}
