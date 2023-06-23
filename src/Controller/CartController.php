<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\ProduitRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    #[Route('/cart', name: 'app_cart')]
    public function index(RequestStack $rs, ProduitRepository $repo): Response
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        $cartWithData = [];
        $total =0;
        foreach($cart as $id => $quantity)
        {
            $product = $repo->find($id);
            $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }
        // dd($cartWithData);
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);

    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart($id, RequestStack $rs): Response
    {
         $session = $rs->getSession();
         $qt = $session->get('qt', 0);
         $cart = $session->get('cart', []);
         if(!empty($cart[$id]))
         {
             $cart[$id]++;
             $qt++;
         } else {
             $cart[$id] = 1;
             $qt++;
         }
         $session->set('cart', $cart);
         $session->set('qt', $qt);
         // dd($session->get('cart'));
         return $this->redirectToRoute('home');
    } 

    #[Route('/cart/add_in/{id}', name: 'in_cart_add')]
    public function addInCart($id, RequestStack $rs): Response
    {
         $session = $rs->getSession();
         $qt = $session->get('qt', 0);
         $cart = $session->get('cart', []);
         if(!empty($cart[$id]))
         {
             $cart[$id]++;
             $qt++;
         } else {
             $cart[$id] = 1;
             $qt++;
         }
         $session->set('cart', $cart);
         $session->set('qt', $qt);
         // dd($session->get('cart'));
         return $this->redirectToRoute('app_cart');
    } 

    #[Route('/cart/minus_in/{id}', name: 'in_cart_minus')]
    public function minusInCart($id, RequestStack $rs): Response
    {
         $session = $rs->getSession();
         $qt = $session->get('qt', 0);
         $cart = $session->get('cart', []);
         if(!empty($cart[$id]) && $qt > 1)
         {
             $cart[$id]--;
             $qt--;
         } else {
            $qt -= $cart[$id];
            unset($cart[$id]);
         }
         $session->set('cart', $cart);
         $session->set('qt', $qt);
        //  dd($session->get('qt'));
         return $this->redirectToRoute('app_cart');
    } 

    #[Route('/cart/remove/{id}', name: 'product_remove')]
    public function removeProduct($id, RequestStack $rs): Response
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);
        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }
        if($qt < 0 )
        {
            $qt = 0;
        }
        $session->set('cart', $cart);
        $session->set('qt', $qt);   
        return $this->redirectToRoute('app_cart');     
    }

    #[Route('/cart/delete', name: 'delete_cart')]
    public function deleteCart(RequestStack $rs): Response
    {
        $session = $rs->getSession();
        $qt = $session->get('qt', 0);
        $qt = 0;
        $session->remove('cart');
        $session->set('qt', $qt);
        return $this->redirectToRoute('home');     
    }

    #[Route('/cart/order', name: 'order')]
    public function commande(EntityManagerInterface $manager, RequestStack $rs, ProduitRepository $repo): Response
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        $cartWithData = [];
        $total =0;
        foreach($cart as $id => $quantity)
        {
            $product = $repo->find($id);
            $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }
        foreach ($cartWithData as $produit) {
        $commande = new Commande;
        $commande->setMembre($this->getUser('id'))
                ->setProduit($produit['product'])
                ->setQuantite($produit['quantity'])
                ->setMontant($produit['product']->getPrice() * $produit['quantity'])
                ->setEtat('encours')
                ->setCreatedAt(new DateTime);

            $stock = $produit['product']->getStock();
            if ($stock < $produit['quantity']){
                $this->addFlash('danger','Commande annulée pour cause de stocks insuffisants');
                return $this->redirectToRoute('app_cart');     
            } else {
            $produit['product']->setStock($stock - $produit['quantity']);
            $manager->persist($produit['product']);                
            }
            $manager->persist($commande);
            $manager->flush();                      
        }
        $qt = $session->get('qt', 0);
        $qt = 0;
        $session->remove('cart');
        $session->set('qt', $qt);
        $this->addFlash('success','Commande passée !');
        return $this->redirectToRoute('home');     
    }
}
