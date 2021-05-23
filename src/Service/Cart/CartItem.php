<?php


namespace App\Service\Cart;

use App\Entity\Variant;

/**
 * Un CarItem est un élément du panier. Il correspond donc à
 * - un objet de type variant
 * - une taille
 * - une quantité
 *
 * La clé d'un item est composé de l'id du variant et de la taille
 * Class CartItem
 * @package App\Service\Cart
 */
class CartItem
{
    private $addTime;
    private $variant;
    private $size;
    private $qty;


    public function __construct(Variant $variant, $size, $qty)
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

    /**
     * Retourne le montant de l'item (qte*prix unitaire)
     *
     * @return float|null
     */
    public function getAmount()
    {
        if(is_numeric($this->getQty()) && is_object($this->getVariant())){
            return (int)$this->getQty()*$this->getVariant()->getPrix();
        }
        return null;
    }
}