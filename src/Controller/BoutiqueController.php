<?php

namespace App\Controller;

use App\Repository\VariantRepository;
use App\Service\Cart\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{

    /**
     * Affiche la catégorie correspondant à $univers, qui est soit 'textile' soit 'chaussant'.
     * Par défaut, ou si univers prend une autre valeur que 'textile' (ou aucune valeur) l'univers Chaussant est affiché
     */
    #[Route('/{univers}', name: 'boutique')]
    public function index(VariantRepository $variantRepository, CartManager $cartManager, $univers='chaussant'): Response
    {
        $listVariants=($univers=='textile')?$variantRepository->getListTextile():$variantRepository->getListChaussant();

        return $this->render('boutique/index.html.twig', [
            'listVariants'=>$listVariants,
            'cartQte'=>$cartManager->getTotalQte()
        ]);
    }
}
