<?php


namespace App\Service\Cart;


use App\Service\DateTimeApp;

/**
 * Interface CartStorageInterface
 * Une classe qui implémente cette interface doit permettre d'enregistrer le panier sur un support (session, bdd, fichier, ..)
 *
 * @package App\Service\Cart
 */
interface CartStorageInterface
{
    /**
     * Retourne la date de création stoquée sur le support
     * @return DateTimeApp|null
     */
    public function getCreationTime(): ?DateTimeApp;

    /**
     * Retorune le contenu du panier stoqué sur le support
     * @return CartContent|null
     */
    public function getCartItems(): ?CartContent;

    /**
     * Ecriture de la date de création
     * @param DateTimeApp $value
     * @return mixed
     */
    public function setCreationTime(DateTimeApp $value);

    /**
     * Ecriture du contenu du panier
     * @param CartContent $value
     * @return mixed
     */
    public function setCartItems(CartContent $value);

    /**
     * Destruction du stockage
     * @return mixed
     */
    public function destroy();
}