<?php


namespace App\Service\Cart;


use App\Service\DateTimeApp;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CartStorageSession
 * Stockage des informations du panier en session
 * @package App\Service\Cart
 */
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
        return $this->session->get(self::_KEY_CREATETIME);
    }

    public function getCartItems():?CartContent
    {
        return $this->session->get(self::_KEY_CARTITEMS);
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
        $this->session->clear();
        return $this;
    }

}