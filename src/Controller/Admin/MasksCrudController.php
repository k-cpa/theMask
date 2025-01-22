<?php

namespace App\Controller\Admin;

use App\Entity\Masks;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MasksCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Masks::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            TextField::new('name'),
            TextEditorField::new('story'),
            // AssociationField::new('user', 'Créateur'),
            // Display de l'image -> Gestion display 'FileType'
            ImageField::new('imageName', 'image')
                ->setBasePath('uploads/images/')
                ->setUploadDir('public/uploads/images/')
        ];
    }
}
