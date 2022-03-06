<?php

namespace App\Controller;

use App\Repository\UserInfoRepository;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact_us")
     */
    public function index(CartManager $cartManager, 
    UserInfoRepository $userInfoRepository): Response
    {
        $cart = $cartManager->getCurrentCart();
        $userName = [];
        if($cart->getId()){
            $userName = $userInfoRepository->findOneBy(['userid' => $cart->getId()]);
            if($userName == null){
                $userName = [];
                $userName['username'] = 'Guest';
            }
        }else{
            $userName['username'] = 'Guest';
        }
        $cart = $cart->getItems()->count() ? $cart : 0;
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'cart' => $cart,
            'userName' => $userName
        ]);
    }
}
