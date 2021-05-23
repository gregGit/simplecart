<?php


namespace App\Services\Cart;


use App\Entity\Variants;

class CartItem
{
    private $addTime;
    private $variant;
    private $size;
    private $qty;


    public function __construct(Variants $variant, $size, $qty)
    {
        $this->setVariant($variant)
            ->setSize($size)
            ->setQty($qty);
        $this->setAddTime(date('YmdHis'));
    }

    public function getKey(){
        return sprintf("%s_%s", $this->getVariant()->getId(), $this->getSize());
    }
    /**
     * @return mixed
     */
    public function getAddTime()
    {
        return $this->addTime;
    }

    /**
     * @param mixed $addTime
     * @return CartItem
     */
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * @param mixed $variant
     * @return CartItem
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     * @return CartItem
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param mixed $qty
     * @return CartItem
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    public function getAmount()
    {
        if(is_numeric($this->getQty()) && is_object($this->getVariant())){
            return (int)$this->getQty()*$this->getVariant()->getPrix();
        }
        return null;
    }
}