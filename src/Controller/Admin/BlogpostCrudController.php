<?php

namespace App\Controller\Admin;

use App\Entity\Blogpost;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class BlogpostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Blogpost::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('slug')->hideOnForm(),
            TextareaField::new('content'),
            DateField::new('dateUpload')->hideOnForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['dateUpload' => 'DESC']);
    }
}
