<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/9/12
 * Time: 18:05
 */

namespace App\Services;


use App\Repository\UserRepository;

class UserServices extends Services
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //获取user表中的值
    public function getUserData()
    {
        return $this->userRepository->getUserData();
    }
}