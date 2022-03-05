<?php

namespace App\Controller;

use App\Form\UserInfoType;
use App\Manager\UserInfoManager;
use App\Manager\CartManager;
use App\Repository\UserInfoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/info", name="user_info")
     */
    public function index(
        CartManager $cartManager,
        UserInfoManager $userInfoManager,
        Request $request,
        UserInfoRepository $userInfoRepository
    ): Response
    {
        $cart = $cartManager->getCurrentCart();
        if($cart->getId()){
            $userInfo = $userInfoRepository->findOneBy(['userid' => $cart->getId()]);
            if($userInfo == null){
                $userInfo['username'] = 'Guest';
                return $this->redirectToRoute('home');
            }
        }else{
            $userInfo['username'] = 'Guest';
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(UserInfoType::class, $userInfo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $userInfoManager->save($userInfo);
            return $this->redirectToRoute('user_info');
        }
        return $this->render('info/index.html.twig', [
            'form' => $form->createView(),
            'userName' => $userInfo,
            'cart' => $cart
        ]);
    }
}
