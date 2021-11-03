<?php

namespace App\Controller\Admin;

use App\Entity\Blogpost;
use App\Entity\Category;
use App\Entity\Pictures;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Pix');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud(
            'Pictures',
            'fas fa-palette',
            Pictures::class
        );
        yield MenuItem::linkToCrud('Category', 'fas fa-tags', Category::class);
    }
}
