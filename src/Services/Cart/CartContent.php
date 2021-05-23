<?php


namespace App\Services\Cart;


class CartContent implements \Iterator
{
    protected $pos=0;
    protected $items=[];

    public function add(CartItem $addItem){
        foreach($this as $key=>$item){
            if($key==$addItem->getKey()){
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