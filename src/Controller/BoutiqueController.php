<?php

namespace App\Controller;

use App\Repository\VariantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/{univers}', name: 'boutique')]
    public function index($univers='chaussant', VariantRepository $variantRepository): Response
    {
        $listVariants=($univers=='textile')?$variantRepository->getListTextile():$variantRepository->getListChaussant();

        return $this->render('boutique/index.html.twig', [
            "listVariants"=>$listVariants
        ]);
    }
}
