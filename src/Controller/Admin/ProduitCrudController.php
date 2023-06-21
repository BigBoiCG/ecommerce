<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextEditorField::new('description')->onlyOnForms(),
            ColorField::new('color'),
            ChoiceField::new('size', 'Taille')->setChoices([
                'XS' => 'xs',
                'S' => 's',
                'M' => 'm',
                'L' => 'l',
                'XL' => 'xl'
            ]),
            ChoiceField::new('collection', 'Collection')->setChoices([
                'Homme' => 'homme',
                'Femme' => 'femme',
                'Enfant' => 'enfant'
            ]),
            ImageField::new('image')->setBasePath('images/produit')->setUploadDir('public/images/produit')->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
            NumberField::new('price', 'Prix'),
            NumberField::new('stock', 'Stock'),
            DateTimeField::new('createdAt')->setFormat('d M Y à H:m:s')->hideOnForm(),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $article = new $entityFqcn;
        $article->setCreatedAt(new DateTime);
        return $article;
    }
}

