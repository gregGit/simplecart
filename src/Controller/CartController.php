<?php

namespace App\Controller;

use App\Repository\VariantRepository;
use App\Services\Cart\CartItem;
use App\Services\Cart\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/content', name: 'cart-content')]
    public function index(CartManager $cartManager): Response
    {
        return $this->render(
            'cart/index.html.twig', [
            'controller_name' => 'CartController',
            'content'=>$cartManager->getItems(),
            'dateShip'=>$cartManager->getShipDate(7),
            'cartTotal'=>$cartManager->getCartTotal(),
            'cartQte'=>$cartManager->getTotalQte()
        ]);
    }

    #[Route('/cart/add', name: 'cart-add')]
    public function add(Request $request,VariantRepository $variantRepository, CartManager $cartManager): Response
    {
        $variantId=$request->request->get('variant');
        $oVariant=$variantRepository->findOneById($variantId);
        $addOk=false;
        $info='';
        if($oVariant){
            $qte=$request->request->get('qte');
            $size=$request->request->get('size');
            $cartManager->addItem(New CartItem($oVariant, $size, $qte));
            if(!$cartManager->hasError()){
                $addOk=true;
                $cartManager->store();
            }else {
                $info=$cartManager->getError();
            }
        }

        return new JsonResponse([
                'addOk'=>$addOk,
                'info'=>$info,
                'cartQte'=>$cartManager->getTotalQte()
            ]
        );
    }
    #[Route('/cart/reset', name: 'cart-reset')]
    public function reset(CartManager $cartManager): Response
    {
        $cartManager->delete();
        return $this->redirectToRoute('boutique');
    }

    protected function isXmlHttpRequest(Request $request) {
        return ($request->isXmlHttpRequest()) ||
            ($this->container->get('kernel')->getEnvironment() == "dev" );
    }
}
