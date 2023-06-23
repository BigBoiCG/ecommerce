<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        $test = 'test';
        return $this->render('app/index.html.twig', [
            'produits' => $produits,
            'test' => $test
        ]);
    }

    #[Route('/profile', name: 'profil_commandes')]
    public function profilCommandes(CommandeRepository $repo): Response
    {   
        $idMembre = $this->getUser('id');
        $commandes = $repo->findBy(['membre' => $idMembre], ['created_at' => 'DESC']);

        return $this->render('app/profile.html.twig', [
            'commandes' => $commandes,
            "editMode" => null
        ]);
    }

    #[Route('/profile/edit/{order}', name: 'user_commande_edit')]
    public function userEditCommandes($order, CommandeRepository $commandeRepo, Request $request, EntityManagerInterface $manager): Response
    {   
        $idMembre = $this->getUser('id');
        $commandes = $commandeRepo->findBy(['membre' => $idMembre], ['created_at' => 'DESC']);        
        $commande = $commandeRepo->findOneBy(['id' => $order]);
        $quantite = $commande->getQuantite();

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        $prixProduit = $commande->getProduit()->getPrice();
        
        // dd($quantite);
        
        if($form->isSubmitted() && $form->isValid())
        {
            
            $modif = $commande->getQuantite();
            // dd($modif);
            $diff = $modif - $quantite;
            // dd($quantite, $modif, $diff);
            $commande->setMontant($modif * $prixProduit);
            $produit = $commande->getProduit();
            $stock = $produit->getStock();
            if ($stock < $diff){
                $this->addFlash('danger','Modification annulée pour cause de stocks insuffisants');
                return $this->redirectToRoute('profil_commandes');     
            } else {
            $produit->setStock($stock + ($diff * -1));
            $manager->persist($produit);                
            }
            $manager->persist($commande); 
            $manager->flush();
            $this->addFlash('success', 'Commande modifiée');
            return $this->redirectToRoute('home');
        }



        return $this->render('app/profile.html.twig', [
            'commandes' => $commandes,
            'editForm' => $form,
            "editMode" => $commande->getId()!=null
        ]);
    }

    #[Route('/profile/delete/{id}', name: 'user_delete_commande')]
    public function userDeleteCommande(Commande $commande, EntityManagerInterface $manager): Response
    {
        $manager->remove($commande);
        $manager->flush();
        $this->addFlash(
            'success',
            'Commande supprimée'); 
        return $this->redirectToRoute('home');
    }
}
