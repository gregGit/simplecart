<?php

namespace App\Controller;

use App\Repository\VariantRepository;
use App\Service\Cart\CartItem;
use App\Service\Cart\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Extra\Intl\IntlExtension;

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
        if(!$request->isXmlHttpRequest()){
            return $this->redirectToRoute('boutique');
        }
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
    #[Route('/cart/remove', name: 'cart-remove')]
    public function remove(Request $request,VariantRepository $variantRepository, CartManager $cartManager): Response
    {
        if(!$request->isXmlHttpRequest()){
            return $this->redirectToRoute('boutique');
        }
        $variantId = $request->request->get('variant');
        $oVariant=$variantRepository->findOneById($variantId);
        $addOk=false;
        $info='';
        if($oVariant) {
            $qte = $request->request->get('qte');
            $size = $request->request->get('size');
            $item = new CartItem($oVariant, $size, $qte);
            $cartManager->removeItem($item);
            if (!$cartManager->hasError()) {
                $addOk = true;
                $cartManager->store();
            } else {
                $info = $cartManager->getError();
            }
        }
        $twExt=new IntlExtension();
        return new JsonResponse([
                'addOk'=>$addOk,
                'info'=>$info,
                'cartQte'=>$cartManager->getTotalQte(),
                'cartAmount'=>$twExt->formatCurrency($cartManager->getCartTotal(), 'EUR')
            ]
        );
    }
    #[Route('/cart/setqty', name: 'cart-setqty')]
    public function setqty(Request $request,VariantRepository $variantRepository, CartManager $cartManager): Response
    {
        if(!$request->isXmlHttpRequest()){
            return $this->redirectToRoute('boutique');
        }
        $variantId = $request->request->get('variant');
        $oVariant=$variantRepository->findOneById($variantId);
        $addOk=false;
        $info='';

        if($oVariant){
            $qte=$request->request->get('qte');
            $size=$request->request->get('size');
            $item=New CartItem($oVariant, $size, $qte);
            $cartManager->setQtyItem($item);
            if(!$cartManager->hasError()){
                $addOk=true;
                $cartManager->store();
            }else {
                $info=$cartManager->getError();
            }
        }

        $twExt=new IntlExtension();
        return new JsonResponse([
                'addOk'=>$addOk,
                'info'=>$info,
                'cartQte'=>$cartManager->getTotalQte(),
                'cartAmount'=>$twExt->formatCurrency($cartManager->getCartTotal(), 'EUR'),
                'itemAmount'=>$addOk?$twExt->formatCurrency($cartManager->getItem(New CartItem($oVariant, $size, $qte))->getAmount(),'EUR'):null
            ]
        );
    }


}
