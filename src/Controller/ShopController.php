<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\AddToCartType;
use App\Form\CartReserveType;
use App\Form\CartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;


/**
 * @Route("/boutique", name="shop_")
 */
class ShopController extends AbstractController
{
    /**
     * @Route("/produit", name="product")
     */
    public function product(): Response
    {

        $ProductRepo = $this->getDoctrine()->getRepository(Product::class);
        $product = $ProductRepo->findAll();
        return $this->render('shop/product.html.twig',[
            'product' => $product,
        ]);
    }
    /**
     * @Route("/produit/{slug}", name="product.detail")
     */
    public function productdesc(Product $product, Request $request, CartManager $cartManager): Response
    {
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime())
            ;


            $cartManager->save($cart);

            return $this->redirectToRoute('shop_product.detail', ['slug' => $product->getSlug()]);
        }

        return $this->render('shop/detail.html.twig',[
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cart", name="panier")
     */
    public function panier(CartManager $cartManager, Request $request): Response
    {
        $cart = $cartManager->getCurrentCart();
        $form = $this->createForm(CartType::class, $cart);
        $formreserve = $this->createForm(CartReserveType::class,$cart);

        $formreserve->handleRequest($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->save($cart);

            return $this->redirectToRoute('shop_panier');
        }
        if ($formreserve->isSubmitted() && $formreserve->isValid()){
            $date = new \DateTime();
            if($cart->getDateReservation() > $date && $cart->getDateReservation() < $date->add(new \DateInterval('P15D')))
            {
                $cart->setBuyer($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('main');
            }else {
                $this->addFlash('error','la date doit etre conforme au maximum 15 jours');
            }


        }

        return $this->render('shop/cart.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
            'formreserve' => $formreserve->createView(),
        ]);
    }

}
