<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Manager\CartManager;
use App\Manager\UserInfoManager;
use App\Repository\ProductRepository;
use App\Repository\UserInfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/{id}", name="product.detail")
     */
    public function index(Product $product, Request $request, CartManager $cartManager, 
    UserInfoRepository $userInfoRepository, UserInfoManager $userInfoManager): Response
    {
        $form = $this->createForm(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());

            $cartManager->save($cart);
            $userName = $userInfoRepository->findOneBy(['userid' => $cart->getId()]);
            if ($userName->getUsername() == null) {
                $userInfoManager->createUserInfo($cart);
            }
            return $this->redirectToRoute('product.detail', ['id' => $product->getId()]);
        }else{
            $cart = $cartManager->getCurrentCart();
            $userName = [];
            if($cart->getId()){
                $userName = $userInfoRepository->findOneBy(['userid' => $cart->getId()]);
                if($userName == null){
                    $userName['username'] = 'Guest';
                }
            }else{
                $userName['username'] = 'Guest';
            }
            $cart = $cart->getItems()->count() ? $cart : 0;
        }

        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'cart' => $cart,        
            'userName' => $userName
        ]);
    }
}