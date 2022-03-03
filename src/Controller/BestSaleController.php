<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Manager\CartManager;
use App\Repository\UserInfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BestSaleController extends AbstractController
{
    /**
     * @Route("/best_sale", name="best_sale")
     */
    public function index(OrderRepository $orderRepository, CartManager $cartManager,
    ProductRepository $productRepository, UserInfoRepository $userInfoRepository): Response
    {
        $listOrder = $orderRepository->findAll();
        // Find all products ordered by list of orders and calculate the total quantity of each product to get the top 10 products best sale by quantity
        $totalQuantity = [];
        foreach ($listOrder as $order) {
            foreach ($order->getItems() as $item) {
                if (array_key_exists($item->getProduct()->getId(), $totalQuantity)) {
                    $totalQuantity[$item->getProduct()->getId()] += $item->getQuantity();
                } else {
                    $totalQuantity[$item->getProduct()->getId()] = $item->getQuantity();
                }
            }
        }
        // Sort the array by quantity
        arsort($totalQuantity);
        // Get the top 10 products and its quantity
        $top10Products = [];
        $topQuantity = [];
        $i = 0;
        foreach ($totalQuantity as $key => $value) {
            if ($i < 10) {
                $top10Products[$key] = $value;
                $topQuantity[$key] = $value;
                $i++;
            }
        }
        // Retrieve the products from the top 10 products by id in product repository
        $top10Products = $productRepository->findBy(['id' => array_keys($top10Products)]);
        // Reverse $top10Products
        $top10Products = array_reverse($top10Products);
        // Cart manager
        $cart = $cartManager->getCurrentCart();
        if($cart->getId()){
            $userName = $userInfoRepository->findOneBy(['userid' => $cart->getId()]);
            if($userName == null){
                $userName['username'] = 'Guest';
            }
        }else{
            $userName['username'] = 'Guest';
        }
        $cart = $cart->getItems()->count() ? $cart : 0;        
        return $this->render('best_sale/index.html.twig', [
            'cart' => $cart,
            'products' => $top10Products,
            'quantity' => $topQuantity,       
            'userName' => $userName
        ]);
    }
}
