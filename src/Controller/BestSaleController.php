<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BestSaleController extends AbstractController
{
    /**
     * @Route("/best_sale", name="best_sale")
     */
    public function index(OrderRepository $orderRepository, CartManager $cartManager, ProductRepository $productRepository): Response
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
        // Get the top 10 products
        $top10Products = [];
        $i = 0;
        foreach ($totalQuantity as $key => $value) {
            if ($i < 10) {
                $top10Products[$key] = $value;
                $i++;
            }
        }
        // Retrieve the products from the top 10 products by id in product repository
        $top10Products = $productRepository->findBy(['id' => array_keys($top10Products)]);
        $cart = $cartManager->getCurrentCart();
        $cart = $cart->getItems()->count() ? $cart : 0;
        return $this->render('best_sale/index.html.twig', [
            'cart' => $cart,
            'products' => $top10Products,
        ]);
    }
}
