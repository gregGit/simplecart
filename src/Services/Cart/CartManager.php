<?php


namespace App\Services\Cart;


use App\Entity\Variants;
use App\Repository\VariantsRepository;

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
        $this->init();
    }

    public function reset(){
        $this->cartCreateTime=date('YmdHis');
        $this->cartItems=new CartContent();
        return $this;
    }

    protected function init(){
        if($this->isInitialized()){
            $this->cartCreateTime=$this->cartStorage->getCreationTime();
            $this->cartItems=$this->cartStorage->getCartItems();
        }else {
            $this->reset();
        }
        $this->error=self::_ERR_NONE;
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
        if(!$this->isInitialized()){
            $this->initialize();
        }
        $this->cartStorage->setCartItems($this->cartItems);
        return $this;
    }

    protected function  initialize(){
        $this->cartStorage->setCreationTime(date('YmdHmi'));
        return $this;
    }
    public function isInitialized()
    {
        $create=$this->cartStorage->getCreationTime();
        return !empty($create);
    }
    public function getTotalQte(){
        $total=0;
        foreach($this->cartItems as $item){
            $total+=$item->getQty();
        }
        return $total;
    }

    /**
     * Retourne la date de livraison
     * La date est calculé à n jours après la date de création.
     * Les Samedis et dimanches sont exclus, de même que tous les jours fériés passé dans le tableau jf
     *
     * @param int $n Nombres de jours à ajouter
     * @param string[] $jf Tableau des jours fériés (chaque date doit être au format dd-mm)
     * @return \DateTime
     * @throws \Exception
     */
    public function getShipDate($n=7, $jf=['01-05','08-05','14-07','15-08','01-11','25-12','31-12']){
        $shipD=new \DateTime($this->cartCreateTime);
        $i=0;
        while($i<$n){
            $shipD->add(new \DateInterval('P1D'));
            if($shipD->format('N')==6||$shipD->format('N')==7||in_array($shipD->format("d-m"), $jf)){
                continue;
            }
            $i++;
        }
        return $shipD;
    }

    public function getCartTotal()
    {
        $total=0;
        foreach($this->cartItems as $item){
            $total+=$item->getAmount();
        }
        return $total;
    }
//    const _CART_CREATE_TIME='cart_create_time';
//    const _CART_ITEMS='cart_items';
//
//    protected $createtime=false;
//    protected $items=[];
//    private $variantRepos;
//    private $error=null;
//    public function __construct(VariantsRepository $variantRepos)
//    {
//        $this->variantRepos=$variantRepos;
//    }
//
//    public function getError(){
//        return $this->error;
//    }
//
//    public function reset()
//    {
//        $this->createtime=date('YmdHis');
//        $this->items=[];
//    }
//    public function getItems()
//    {
//        return $this->items;
//    }
//    public function getCreateTime(){
//        return $this->createtime;
//    }
//
//    public function addVariant($variantId, $size, $qte)
//    {
//        $variant=$this->getVariantRow($variantId);
//        $stock=$variant->getStock($size);
//
//        $itemIndex=$this->getItemIndex($variant, $size);
//        if($itemIndex===false){
//            $itemIndex=count($this->items);
//            $requiredQte=$qte;
//        }else {
//            $requiredQte=$this->items[$itemIndex]["qte"]+$qte;
//        }
//        if($requiredQte>$variant->getStock($size)){
//            $this->error="Il n'y a pas suffisement d'unité en stock";
//            return false;
//        }
//        $this->storeItem($variant,$size, $qte)
//            ->store();
//        return true;
//    }
//
//    public function getQteCount(){
//        $qte=0;
//        foreach ($this->items as $item){
//            $qte+=$item["qte"];
//        }
//        return $qte;
//    }
//    private function getItemIndex($variant, $size)
//    {
//        foreach ($this->items as $index => $item) {
//            if ($item["oVariant"]->getId() == $variant->getId() && $item["size"] == $size) {
//                return $index;
//            }
//        }
//        return false;
//    }
//    protected function storeItem($variant, $size, $qte){
//        foreach($this->items as $index=>$item){
//            if($item["oVariant"]->getId()==$variant->getId() && $item["size"]==$size){
//                $this->items[$index]["oVariant"]=$variant;
//                $this->items[$index]["qte"]+=$qte;
//                return $this;
//            }
//        }
//
//        $this->items[]=[
//            "oVariant"=>$variant,
//            "size"=>$size,
//            "qte"=>$qte
//        ];
//        return $this;
//    }
//    protected function getVariantRow($variantId){
//         return $this->variantRepos->findOneById($variantId);
//    }
//
//
    }