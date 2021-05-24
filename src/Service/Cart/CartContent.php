<?php


namespace App\Service\Cart;

/**
 * CartContent est une liste de CarItem, donc au contenu du panier.
 * La classe implémente iterator pour pouvoir être itérée
 *
 * Class CartContent
 * @package App\Service\Cart
 */
class CartContent implements \Iterator
{
    protected $pos=0;
    protected $items=[];

    /**
     * Ajoute ou met à jour un item.
     * Ajoute si le variant est absent de la liste
     * Met à jour sinon en ajoutant la quantité, et en écrasant le variant (et donc le stock si celui-ci a changé)
     * @param CartItem $addItem
     * @return $this
     */
    public function add(CartItem $addItem){
        foreach($this as $key=>$item){
            if($key==$addItem->getKey())
            {
                $item->setVariant($addItem->getVariant());
                $item->setQty($item->getQty()+$addItem->getQty());
                return $this;
            }
        }
        $this->items[]=$addItem;
        return $this;
    }

    public function current(): ?CartItem
    {
        return count($this->items)>0?$this->items[$this->pos]:null;
    }

    public function next()
    {
        $this->pos++;
    }

    public function key()
    {
        if(count($this->items)>0){
            return $this->current()->getKey();
        }
        return null;
    }

    public function valid()
    {
        $maxpos=count($this->items)-1;
       return $this->pos<=$maxpos;
    }

    public function rewind()
    {
        $this->pos=0;
    }
}