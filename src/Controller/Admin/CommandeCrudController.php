<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureActions(Actions $actions): Actions
{
    return $actions
        ->remove(Crud::PAGE_INDEX, Action::NEW)
    ;
}


    // public function configureFields(string $pageName): iterable
    // {
    //     return [
    //         IdField::new('id')->hideOnForm(),
    //         IdField::new('membre')->hideOnForm(),
    //         IdField::new('produit')->hideOnForm(),
    //         NumberField::new('quantite'),
    //     ];
    // }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            // IdField::new('membre')->hideOnForm(),
            // IdField::new('produit')->hideOnForm(),
            NumberField::new('quantite'),
            NumberField::new('montant'),
            ChoiceField::new('etat')->setChoices([
                'En cours de traitement' => 'encours',
                'Envoyé' => 'envoye',
                'Livré' => 'livre'
            ]),
            DateTimeField::new('createdAt')->setFormat('d M Y à H:m:s')->hideOnForm(),

        ];
    }


}
