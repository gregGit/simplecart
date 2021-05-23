<?php


namespace App\Service\Cart;


use App\Service\DateTimeApp;

/**
 * Gestionnaire du pannier.
 * Fournit les méthodes permettant la gestion du panier (ajout, suppression, ...)
 * La classe à besoin d'un objet qui implément CartStorageInterface pour assurer les opérations de stockage des données du paniers.
 *
 * Class CartManager
 * @package App\Service\Cart
 */
class CartManager
{
    const _ERR_NONE=0;
    const _ERR_STOCK=1;

    private $cartCreateTime;
    private $cartItems;
    private $cartStorage;
    private $error;

    /**
     *
     * Un objet de type CartStorageInterface doit être injecté pour que la classe fonctionne
     * CartManager constructor.
     * @param CartStorageInterface $cartStorage
     */
    public function __construct(CartStorageInterface $cartStorage, DateTimeApp $cartCreateTime)
    {
        $this->cartStorage=$cartStorage;
        $this->initialize($cartCreateTime);
    }

    /**
     * Initialisation
     * Lecture des informations sur le stockage.
     * Si le stockage est vide initialise cartCreateTime et cartItems
     * @return $this
     */
    protected function  initialize($cartCreateTime){
        $this->load();
        $this->error=self::_ERR_NONE;
        if(empty($this->cartCreateTime)){
            $this->cartCreateTime=$cartCreateTime;
        }
        if(empty($this->cartItems)){
            $this->cartItems=new CartContent();
        }
        return $this;
    }

    /**
     * Chargement
     * Lecture de cartCreateTime et cartItems depuis le stockage
     * @return $this
     */
    protected function load(){
        $this->cartCreateTime=$this->cartStorage->getCreationTime();
        $this->cartItems=$this->cartStorage->getCartItems();
        return $this;
    }

    /**
     * RAZ panier
     * Le panier est supprimé sur le stockage puis réinitialisé
     * @return $this
     */
    public function reset(){
        $this->delete();
        $this->initialize();
        return $this;
    }

    /**
     * Suppression
     * Suppression du panier, y compris sur le stockage
     * @return $this
     */
    public function delete(){
        $this->cartItems=null;
        $this->cartCreateTime=null;
        $this->cartStorage->destroy();
        return $this;
    }

    /**
     * Ajout d'un élément
     * Ajoute un cartItem au panier
     * Avant l'ajout vérifie que la quantité à ajouter est bien en stock (y compris si l'item est déjà dans le panier)
     * En cas d'erreur aliment $error avec un code erreur
     * @param CartItem $cartItem
     * @return bool
     */
    public function addItem(CartItem $cartItem){
        if($this->verifyQte($cartItem)){
            $this->cartItems->add($cartItem);
            return true;
        } else {
            $this->error=self::_ERR_STOCK;
            return false;
        }
    }

    /**
     * Vérification stock
     * Vérifie que le stock pour l'item est suffisant, en tenant compte :
     * - du stock
     * - de la quantité à ajouter
     * - de la quantité déjà présente dans le panier (si l'item existe déjà)
     * @param CartItem $cartItem
     * @return bool
     */
    protected function verifyQte(CartItem $cartItem){
        $qtyInCart=$this->qtyInCart($cartItem);
        $stock=$cartItem->getVariant()->getStock($cartItem->getSize());
        return $qtyInCart+$cartItem->getQty()<=$stock;
    }

    /**
     * Quantité dans le panier
     * Retrouve la quantité déjà présente dans le panier pour CartItem
     * @param CartItem $searchItem
     * @return int
     */
    protected function qtyInCart(CartItem $searchItem){
        foreach ($this->cartItems as $key=>$item){
            if($searchItem->getKey()==$item->getKey()){
                return $item->getQty();
            }
        }
        return 0;
    }

    /**
     * @return CartContent|null
     */
    public function getItems(): ?CartContent{
        return $this->cartItems;
    }

    /**
     * @return bool
     */
    public function hasError(){
        return $this->error!=self::_ERR_NONE;
    }

    /**
     * @return mixed
     */
    public function getError(){
        return $this->error;
    }

    /**
     * Enregistrement
     * Enregistre les informations du panier sur le stockage
     * @return $this
     */
    public function store(){
        $this->cartStorage->setCartItems($this->cartItems);
        return $this;
    }


    /**
     * Quantité totale
     * Calcul du nombre total d'articles dans le panier (somme de quantité des items)
     * @return int
     */
    public function getTotalQte(){
        $total=0;
        foreach($this->cartItems as $item){
            $total+=$item->getQty();
        }
        return $total;
    }

    /**
     * Montant total
     * Calcul le montant total du panier
     * @return int
     */
    public function getCartTotal()
    {
        $total=0;
        foreach($this->cartItems as $item){
            $total+=$item->getAmount();
        }
        return $total;
    }

    /**
     * Date d'expédition
     * Retrouve la date d'expédition = datecreation+$n jours ouvrés
     *
     * @param int $n
     * @return DateTimeApp
     */
    public function getShipDate($n=7):?DateTimeApp{
        if(empty($this->cartCreateTime)){
            return null;
        }
        $shipDate=clone $this->cartCreateTime;
        return $shipDate->addWorkingDays($n);
    }
}