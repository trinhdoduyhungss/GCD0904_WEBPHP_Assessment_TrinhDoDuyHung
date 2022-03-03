<?php

namespace App\Controller;

use App\Form\CartType;
use App\Manager\CartManager;
use App\Repository\UserInfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartController
 * @package App\Controller
 */
class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartManager $cartManager, Request $request, UserInfoRepository $userInfoRepository): Response
    {
        $cart = $cartManager->getCurrentCart();
        if($cart->getId()){
            $userName = $userInfoRepository->findOneBy(['userid' => $cart->getId()]);
            if($userName == null){
                $userName['username'] = 'Guest';
            }
        }else{
            $userName['username'] = 'Guest';
        }
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->save($cart);

            return $this->redirectToRoute('cart');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),        
            'userName' => $userName
        ]);
    }
}