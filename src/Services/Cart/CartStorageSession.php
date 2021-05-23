<?php


namespace App\Services\Cart;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartStorageSession implements CartStorageInterface
{
    const _KEY_CREATETIME='created_at';
    const _KEY_CARTITEMS='cart_items';

    private $session;

    public function __construct(SessionInterface $session){
        $this->session=$session;
    }
    public function getCreationTime()
    {
        return $this->session->get(self::_KEY_CREATETIME, false);
    }

    public function getCartItems()
    {
        return $this->session->get(self::_KEY_CARTITEMS, false);
    }

    public function setCreationTime($value)
    {
        $this->session->set(self::_KEY_CREATETIME, $value);
        return $this;
    }

    public function setCartItems($value)
    {
        $this->session->set(self::_KEY_CARTITEMS, $value);
        return $this;
    }


}