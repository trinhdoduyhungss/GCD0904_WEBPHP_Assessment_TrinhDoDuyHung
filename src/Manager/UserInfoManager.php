<?php

namespace App\Manager;

use App\Entity\UserInfo;
use App\Entity\Order;
use App\Repository\UserInfoRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserInfoManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserInfoRepository
     */
    private $userInfoRepository;

    /**
     * UserManager constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->userInfoRepository = $entityManager->getRepository(UserInfo::class);
    }

    /**
     * Creat an user info from the cart id.
     */
    public function createUserInfo(Order $orderId): UserInfo
    {
        $userInfo = new UserInfo();
        $userInfo
            ->setUserid($orderId)
            ->setUsername('Anonymous');
        $this->entityManager->persist($userInfo);
        $this->entityManager->flush();
        return $userInfo;
    }

    // /**
    //  * Persists the user info in database.
    //  */
    // public function save(UserInfo $userInfo): void
    // {
    //     $this->entityManager->persist($userInfo);
    //     $this->entityManager->flush();
    // }
}