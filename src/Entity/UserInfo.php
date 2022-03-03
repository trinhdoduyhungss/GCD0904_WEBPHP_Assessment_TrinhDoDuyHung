<?php

namespace App\Entity;

use App\Repository\UserInfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInfoRepository::class)
 */
class UserInfo
{
    /**
     * @ORM\Id @ORM\OneToOne(targetEntity=Order::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $userid;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username = 'Anonymous';

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserid(): ?Order
    {
        return $this->userid;
    }

    public function setUserid(Order $userid): self
    {
        $this->userid = $userid;

        return $this;
    }
}
