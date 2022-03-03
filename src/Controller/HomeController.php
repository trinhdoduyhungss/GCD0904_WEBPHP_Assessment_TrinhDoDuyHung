<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\UserInfoRepository;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PaginatorInterface $paginator , Request $request, 
        ProductRepository $productRepository, CartManager $cartManager, 
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
        $productsPagination = $paginator->paginate(
            $productRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('home/index.html.twig',[
            'cart' => $cart,
            'products' => $productsPagination, 
            'userName' => $userName
        ]);
    }
}
