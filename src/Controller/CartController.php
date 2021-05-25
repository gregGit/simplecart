<?php

namespace App\Controller;

use App\Repository\VariantRepository;
use App\Service\Cart\CartItem;
use App\Service\Cart\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\RuntimeError;
use Twig\Extra\Intl\IntlExtension;

class CartController extends AbstractController
{
    /**
     * Affichage du contenu du panier
     * @param CartManager $cartManager
     * @return Response
     */
    #[Route('/cart/content', name: 'cart-content')]
    public function index(CartManager $cartManager): Response
    {
        return $this->render(
            'cart/index.html.twig', [
            'content' => $cartManager->getItems(),
            'dateShip' => $cartManager->getShipDate($this->getParameter('app.delaisExp')),
            'cartTotal' => $cartManager->getCartTotal(),
            'cartQte' => $cartManager->getTotalQte()
        ]);
    }


    /**
     * Ajout  d'un élement au panier
     * Action associé à une requête ajax depuis la page du panier
     * LA quantité sera ajoutée si le variant est déja dans le panier
     *
     * @param Request $request
     * @param VariantRepository $variantRepository
     * @param CartManager $cartManager
     * @return Response
     */
    #[Route('/cart/add', name: 'cart-add')]
    public function add(Request $request, VariantRepository $variantRepository, CartManager $cartManager): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('boutique');
        }
        list($variantId, $qte, $size) = $this->parsePostParams($request, ['variant', 'qte', 'size']);
        $oVariant = $variantRepository->findOneById($variantId);
        $execOk = false;

        if ($oVariant) {
            $cartManager->addItem(new CartItem($oVariant, $size, $qte));
            if (!$cartManager->hasError()) {
                $cartManager->store();
                $execOk = true;
            }
        }

        return new JsonResponse([
                'addOk' => $execOk,
                'info' => !$execOk ? $cartManager->getError() : '',
                'cartQte' => $cartManager->getTotalQte()
            ]
        );
    }

    /**
     * Suppression du panier
     * Après la suppression l'utilisateur est redirigé sur la boutique
     * @param CartManager $cartManager
     * @return Response
     */
    #[Route('/cart/reset', name: 'cart-reset')]
    public function reset(CartManager $cartManager): Response
    {
        $cartManager->delete();
        return $this->redirectToHome();
    }

    /**
     * Suppression d'un élément du panier
     * Action associé à une requête ajax depuis la page du panier
     *
     * @param Request $request
     * @param VariantRepository $variantRepository
     * @param CartManager $cartManager
     * @return Response
     * @throws RuntimeError
     */
    #[Route('/cart/remove', name: 'cart-remove')]
    public function remove(Request $request, VariantRepository $variantRepository, CartManager $cartManager): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToHome();
        }
        list($variantId, $size) = $this->parsePostParams($request, ['variant', 'size']);
        $oVariant = $variantRepository->findOneById($variantId);
        $execOk = false;
        if ($oVariant) {
            $cartManager->removeItem(new CartItem($oVariant, $size));
            if (!$cartManager->hasError()) {
                $cartManager->store();
                $execOk = true;
            }
        }
        $twExt = new IntlExtension();
        return new JsonResponse([
                'addOk' => $execOk,
                'info' => !$execOk ? $cartManager->getError() : '',
                'cartQte' => $cartManager->getTotalQte(),
                'cartAmount' => $twExt->formatCurrency($cartManager->getCartTotal(), 'EUR')
            ]
        );
    }

    /**
     * Modification d'une quantité pour un variant deja dans le panier
     * Action associé à une requête ajax depuis la page du panier
     *
     * @param Request $request
     * @param VariantRepository $variantRepository
     * @param CartManager $cartManager
     * @return Response
     * @throws RuntimeError
     */
    #[Route('/cart/setqty', name: 'cart-setqty')]
    public function setqty(Request $request, VariantRepository $variantRepository, CartManager $cartManager): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToHome();
        }
        list($variantId, $qte, $size) = $this->parsePostParams($request, ['variant', 'qte', 'size']);
        $oVariant = $variantRepository->findOneById($variantId);
        $execOk = false;
        $item=null;

        if ($oVariant) {
            $item = new CartItem($oVariant, $size, $qte);
            $cartManager->setQtyItem($item);
            if (!$cartManager->hasError()) {
                $cartManager->store();
                $execOk = true;
            }
        }

        $twExt = new IntlExtension();
        return new JsonResponse([
                'addOk' => $execOk,
                'info' => !$execOk ? $cartManager->getError() : '',
                'cartQte' => $cartManager->getTotalQte(),
                'cartAmount' => $twExt->formatCurrency($cartManager->getCartTotal(), 'EUR'),
                'itemAmount' => $execOk ? $twExt->formatCurrency($cartManager->getItem($item)->getAmount(), 'EUR') : null
            ]
        );
    }


    /**
     * Retourne un tableau contenant la liste des valeurs des paramètres posts désignés par $paramToParse
     * Les paramètes qui sont absents sont remplacés par $default
     * @param Request $request
     * @param array $paramToParse
     * @param null $default
     * @return array
     */
    protected function parsePostParams(Request $request, array $paramToParse, $default = null)
    {
        $values = [];
        foreach ($paramToParse as $paramName) {
            $values[] = $request->request->get($paramName, $default);
        }
        return $values;
    }

    /**
     * renvoi une redirection vers la page boutique
     * @return RedirectResponse
     */
    protected function redirectToHome()
    {
        return $this->redirectToRoute('boutique');
    }
}
