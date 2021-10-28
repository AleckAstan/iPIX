<?php

namespace App\Controller;

use App\Entity\Pictures;
use App\Form\PicturesType;
use App\Repository\PicturesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pictures/c/r/u/d")
 */
class PicturesCRUDController extends AbstractController
{
    /**
     * @Route("/", name="pictures_c_r_u_d_index", methods={"GET"})
     */
    public function index(PicturesRepository $picturesRepository): Response
    {
        return $this->render('pictures_crud/index.html.twig', [
            'pictures' => $picturesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pictures_c_r_u_d_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $picture = new Pictures();
        $form = $this->createForm(PicturesType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get image file

            $image = $form->get('imageFile')->getData();

            $entityManager = $this->getDoctrine()->getManager();
            ///get image file

            $content = file_get_contents($image);
            $image = base64_encode($content);

            $picture->setFile($image);
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute(
                'pictures_c_r_u_d_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('pictures_crud/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="pictures_c_r_u_d_show", methods={"GET"})
     */
    public function show(Pictures $picture): Response
    {
        return $this->render('pictures_crud/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pictures_c_r_u_d_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pictures $picture): Response
    {
        $form = $this->createForm(PicturesType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute(
                'pictures_c_r_u_d_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('pictures_crud/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="pictures_c_r_u_d_delete", methods={"POST"})
     */
    public function delete(Request $request, Pictures $picture): Response
    {
        if (
            $this->isCsrfTokenValid(
                'delete' . $picture->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'pictures_c_r_u_d_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
