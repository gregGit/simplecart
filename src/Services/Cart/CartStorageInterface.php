<?php


namespace App\Services\Cart;


interface CartStorageInterface
{
    public function getCreationTime();
    public function getCartItems();
    public function setCreationTime($value);
    public function setCartItems($value);

}