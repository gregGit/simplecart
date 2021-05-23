<?php


namespace App\Services\Cart;


use App\Services\DateTimeApp;

interface CartStorageInterface
{
    public function getCreationTime():?DateTimeApp;
    public function getCartItems():?CartContent;
    public function setCreationTime(DateTimeApp $value);
    public function setCartItems(CartContent $value);
    public function destroy();
}