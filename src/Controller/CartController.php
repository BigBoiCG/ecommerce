<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
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

    #[Route('/cart/remove/{id}', name: 'product_remove')]
    public function deleteProduct($id, RequestStack $rs): Response
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
}
