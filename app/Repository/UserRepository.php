<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/9/12
 * Time: 18:00
 */

namespace App\Repository;

use App\Models\User;

class UserRepository extends Repository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    //获取user表中的值
    public function getUserData()
    {
        return $this->user->all();
    }
}