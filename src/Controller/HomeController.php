<?php

namespace App\Controller;

use App\Repository\ProductRepository;
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
        ProductRepository $productRepository, CartManager $cartManager): Response
    {
        $cart = $cartManager->getCurrentCart();
        $cart = $cart->getItems()->count() ? $cart : 0;
        $productsPagination = $paginator->paginate(
            $productRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('home/index.html.twig',[
            'cart' => $cart,
            'products' => $productsPagination, 
        ]);
    }
}
