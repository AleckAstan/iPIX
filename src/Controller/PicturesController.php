<?php

namespace App\Controller;

use App\Repository\PicturesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PicturesController extends AbstractController
{
    /**
     * @Route("/pictures", name="pictures")
     */
    public function index(
        PicturesRepository $picturesRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $data = $picturesRepository->findAll();

        $pictures = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            6 //content per page
        );
        return $this->render('pictures/index.html.twig', [
            'pictures' => $pictures,
        ]);
    }
}