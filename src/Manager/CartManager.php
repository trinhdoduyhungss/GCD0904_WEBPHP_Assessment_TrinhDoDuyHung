<?php

namespace App\Manager;

use App\Entity\Order;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;

class CartManager
{
    /**
     * @var CartSessionStorage
     */
    private $cartSessionStorage;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * CartManager constructor.
     */
    public function __construct(
        CartSessionStorage $cartStorage,
        EntityManagerInterface $entityManager
    ) {
        $this->cartSessionStorage = $cartStorage;
        $this->entityManager = $entityManager;
    }
    
    /**
     * Create an order from the cart.
     */
    public function create(): Order
    {
        $order = new Order();
        $order
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());   
        return $order;
    }


    /**
     * Gets the current cart.
     */
    public function getCurrentCart(): Order
    {
        $cart = $this->cartSessionStorage->getCart();

        if (!$cart) {
            $cart = $this->create();
        }

        return $cart;
    }

    /**
     * Persists the cart in database and session.
     */
    public function save(Order $cart): void
    {
        // check if quantity is not null and higher than 0
        foreach ($cart->getItems() as $orderItem) {
            if ($orderItem->getQuantity() === null || $orderItem->getQuantity() <= 0) {
                $cart->removeItem($orderItem);
            }
        }
        // Persist in database
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        // Persist in session
        $this->cartSessionStorage->setCart($cart);
    }
}