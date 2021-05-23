<?php


namespace App\Services\Cart;


use App\Services\DateTimeApp;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartStorageSession implements CartStorageInterface
{
    const _KEY_CREATETIME='created_at';
    const _KEY_CARTITEMS='cart_items';

    private $session;

    public function __construct(SessionInterface $session){
        $this->session=$session;
    }
    public function getCreationTime():?DateTimeApp
    {
        return $this->session->get(self::_KEY_CREATETIME, null);
    }

    public function getCartItems():?CartContent
    {
        return $this->session->get(self::_KEY_CARTITEMS, null);
    }

    public function setCreationTime(DateTimeApp $value)
    {
        $this->session->set(self::_KEY_CREATETIME, $value);
        return $this;
    }

    public function setCartItems(CartContent $value)
    {
        $this->session->set(self::_KEY_CARTITEMS, $value);
        return $this;
    }

    public function destroy()
    {
        $this->session->remove(self::_KEY_CARTITEMS);
        $this->session->remove(self::_KEY_CREATETIME);
        return $this;
    }

}