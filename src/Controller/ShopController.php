<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AddProductType;
use App\Form\AddToCartType;
use App\Form\CartReserveType;
use App\Form\CartType;
use App\Manager\CartManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Storage\CartSessionStorage;


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
     * @Route("/mes-reservation/{id}", name="reservation_client")
     * @Security ("is_granted('ROLE_ADMIN')")
     */
    public function reservation(): Response
    {

        $orderRepo = $this->getDoctrine()->getRepository(Order::class);
         $order = $orderRepo->findByBuyer($this->getUser());
        return $this->render('shop/reservation.html.twig',[
           'order' => $order
        ]);
    }

    /**
     * @Route("/ajouter-produit", name="product.add")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function productadd(Request $request): Response
    {

        $product = new Product();
        $form = $this->createForm(AddProductType::class,$product);
        $form->handleRequest($request);
        dump($product);
        if ($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('shop_product');

        }


        return $this->render('shop/addproduct.html.twig',[
            'form' => $form->createView()

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
    public function panier(CartManager $cartManager, Request $request, CartSessionStorage $cartSessionStorage): Response
    {
        $cart = $cartManager->getCurrentCart();
        $form = $this->createForm(CartType::class, $cart);
        $formreserve = $this->createForm(CartReserveType::class,$cart);
        dump($cart);

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
                $cartSessionStorage->deleteCart();

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
    /**
     * @Route("/modif-produit/{slug}", name="product_modif")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function modifProduit(Request $request, Product $product): Response
    {

        $form = $this->createForm(AddProductType::class,$product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('shop_product');

        }


        return $this->render('shop/modifproduct.html.twig',[
            'form' => $form->createView()

        ]);
    }
    /**
     * @Route("/reservation", name="reservation")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function reservationclient(): Response
    {

        $orderRepo = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepo->findall();
        return $this->render('shop/reservationall.html.twig',[
            'order' => $order
        ]);

    }


}
