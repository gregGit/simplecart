<?php


namespace App\Services\Cart;


use App\Services\DateTimeApp;

class CartManager
{
    const _ERR_NONE=0;
    const _ERR_STOCK=1;

    private $cartCreateTime;
    private $cartItems;
    private $cartStorage;
    private $error;
    public function __construct(CartStorageInterface $cartStorage)
    {
        $this->cartStorage=$cartStorage;
        $this->initialize();
    }
    protected function  initialize(){
        $this->load();
        $this->error=self::_ERR_NONE;
        if(empty($this->cartCreateTime)){
            $this->cartCreateTime=new DateTimeApp();
        }
        if(empty($this->cartItems)){
            $this->cartItems=new CartContent();
        }
        return $this;
    }
    protected function load(){
        $this->cartCreateTime=$this->cartStorage->getCreationTime();
        $this->cartItems=$this->cartStorage->getCartItems();
        return $this;
    }

    public function reset(){
        $this->cartStorage->destroy();
        $this->initialize();
        return $this;
    }
    public function delete(){
        $this->cartStorage->destroy();
        return $this;
    }


    public function addItem(CartItem $cartItem){
        if($this->verifyQte($cartItem)){
            $this->cartItems->add($cartItem);
            return true;
        } else {
            $this->error=self::_ERR_STOCK;
            return false;
        }
    }

    protected function verifyQte(CartItem $cartItem){
        $qtyInCart=$this->qtyInCart($cartItem);
        $stock=$cartItem->getVariant()->getStock($cartItem->getSize());
        return $qtyInCart+$cartItem->getQty()<=$stock;
    }

    protected function qtyInCart(CartItem $searchItem){
        foreach ($this->cartItems as $key=>$item){
            if($searchItem->getKey()==$item->getKey()){
                return $item->getQty();
            }
        }
        return 0;
    }


    public function getItems(){
        return $this->cartItems;
    }

    public function hasError(){
        return $this->error!=self::_ERR_NONE;
    }
    public function getError(){
        return $this->error;
    }

    public function store(){
        $this->cartStorage->setCartItems($this->cartItems);
        return $this;
    }


    public function getTotalQte(){
        $total=0;
        foreach($this->cartItems as $item){
            $total+=$item->getQty();
        }
        return $total;
    }


    public function getCartTotal()
    {
        $total=0;
        foreach($this->cartItems as $item){
            $total+=$item->getAmount();
        }
        return $total;
    }

    public function getShipDate($n){
        $shipDate=clone $this->cartCreateTime;
        return $shipDate->addWorkingDays(7);
    }
    }