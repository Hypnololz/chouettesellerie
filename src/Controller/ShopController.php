<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\AddProductType;
use App\Form\AddToCartType;
use App\Form\CartReserveType;
use App\Form\CartType;
use App\Form\ProductModifType;
use App\Manager\CartManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Storage\CartSessionStorage;


/**
 * @Route("/boutique", name="shop_")
 */
class ShopController extends AbstractController
{






    //display produit list
    /**
     * @Route("/produit", name="product")
     */
    public function product(Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT a FROM App\Entity\Product a');

        $pageProduct = $paginator->paginate(
            $query,
            $requestedPage,
            16
        );

        return $this->render('shop/product.html.twig',[
            'product' => $pageProduct,
        ]);
    }

    // envoie des reservation pour le client dans la vue

    /**
     * @Route("/mes-reservation/{id}", name="reservation_client")
     *
     */
    public function reservation(): Response
    {

        $orderRepo = $this->getDoctrine()->getRepository(Order::class);
         $order = $orderRepo->findByBuyer($this->getUser());
        return $this->render('shop/reservation.html.twig',[
           'order' => $order
        ]);
    }


    //add product + photo rename

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
            $photo = $form->get('photo')->getData();
            do{
                $newFileName = md5(random_bytes(100)).'.'. $photo->guessExtension();

            }while(file_exists('public/img/produit/'. $newFileName));
            $product->setPhoto($newFileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $photo->move(
                'img/produit/',
                $newFileName
            );

            return $this->redirectToRoute('shop_product');

        }


        return $this->render('shop/addproduct.html.twig',[
            'form' => $form->createView()

        ]);
    }


    //produit en détail avec ajout au panier

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
            if ($product->getStock() >= $item->getQuantity()){

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime())
            ;


            $cartManager->save($cart);

            return $this->redirectToRoute('shop_product.detail', ['slug' => $product->getSlug()]);
            }else{
                $this->addFlash('error','Le produit n\'est plus en stock');
            }

        }

        return $this->render('shop/detail.html.twig',[
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    // panier + form reservation

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
            //verif pour etre dans une periode de 15 jours
            if($cart->getDateReservation() > $date && $cart->getDateReservation() < $date->add(new \DateInterval('P15D')))
            {
                $cartSessionStorage->deleteCart();

                $cart->setBuyer($this->getUser());
                $id = $cart->getId();
                $em = $this->getDoctrine()->getManager();
                $orderitemrepo = $em->getRepository(OrderItem::class);
                $orderitem =  $orderitemrepo->findByOrderRef($id);
                foreach ( $orderitem as $item){
                    $product = $item->getProduct();
                    $product->setStock($product->getStock() - $item->getQuantity());
                }

                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('success','votre panier a bien etais réserver');
                return $this->redirectToRoute('shop_reservation_client', [
                    'id' => $this->getUser()->getId()
                    ]
                );
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


    //modification des produit administration

    /**
     * @Route("/modif-produit/{slug}", name="product_modif")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function modifProduit(Request $request, Product $product): Response
    {

        $form = $this->createForm(ProductModifType::class,$product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('shop_stock');

        }


        return $this->render('shop/modifproduct.html.twig',[
            'form' => $form->createView()

        ]);
    }

    //envoie des reservation de tout les clients dans la vue

    /**
     * @Route("/reservation", name="reservation")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function reservationclient(): Response
    {

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT a FROM App\Entity\Order a WHERE a.buyer is not null ");
        $order = $query->getResult();
        return $this->render('shop/reservationall.html.twig',[
            'order' => $order
        ]);

    }

    /**
     * @Route("/validation{id}", name="reservation.validation")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function validationreservationclient(Order $order): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();


        return $this->redirectToRoute('shop_reservation');

    }


    //supression de produit via leur id + protection contre csrf

    /**
     * @Route("/produit/delete/{id}", name="produit.delete")
     * @Security ("is_granted('ROLE_ADMIN')")
     */
    public function produitDelete(Product $product, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('product_delete_' . $product->getId(), $request->query->get('csrf_token'))) {
            $this->addFlash('error', 'token secu invalide reessayer');
        } else {

            $em = $this->getDoctrine()->getManager();

            $em->remove($product);

            $em->flush();

            $this->addFlash('success', 'le produit a bien etais surpprimé');

        }
        return $this->redirectToRoute('shop_stock');


    }

    //envoie des produit via la recherche de la navbar

    /**
     * @Route("/produit-recherche", name="search")
     */
    public function shopsearch(Request $request): Response
    {


        $research = $request->query->get('searcharea');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT a FROM App\Entity\Product a WHERE a.name LIKE :key OR a.description LIKE :key ")->setParameter('key', '%' . $research . '%');
        $product = $query->getResult();

        return $this->render('shop/search.html.twig', [
            'product' => $product
        ]);
    }

    //envoie des produits par gammes via les boutons frontpage

    /**
     * @Route("/produit-gammes", name="gammes")
     */
    public function shopgammes(Request $request): Response
    {


        $research = $request->query->get('searcharea');
        $em = $this->getDoctrine()->getManager();

        $querybuild = $em->createQueryBuilder('a')
            ->select('a')
            ->from('App\Entity\Product','a')
            ->innerJoin('a.gammes','b')
            ->where('b.name = :name')
            ->setParameter(':name',$research)
            ->getQuery()
            ->getResult();
        return $this->render('shop/search.html.twig', [
            'product' => $querybuild
        ]);
    }

    //stock et gestion de celui ci

    /**
     * @Route("/produit-stock", name="stock")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function stock(Request $request): Response
    {


        $em = $this->getDoctrine()->getManager();
        $productsRepo = $em->getRepository(Product::class);
        $products = $productsRepo->findBy([],['stock' => 'ASC']);

        return $this->render('shop/stock.html.twig', [
            'products' => $products
        ]);
    }
    /**
     * @Route("/produit-annulation{id}", name="cart.cancel")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function cartCancel(Order $order): Response
    {


        $id = $order->getId();
        $em = $this->getDoctrine()->getManager();
        $orderitemrepo = $em->getRepository(OrderItem::class);
        $orderitem =  $orderitemrepo->findByOrderRef($id);
        foreach ( $orderitem as $item){
            $product = $item->getProduct();
            $product->setStock($product->getStock() + $item->getQuantity());
        }
        $em->remove($order);
        $em->flush();

        return $this->redirectToRoute('shop_reservation');
    }

    /**
     * @Route("/annulationpannier", name="cart.cancel.all")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function cartCancelall(): Response
    {

        $em = $this->getDoctrine()->getManager();

        $querybuild = $em->createQueryBuilder('a')
            ->select('a')
            ->from('App\Entity\OrderItem','a')
            ->innerJoin('a.orderRef','b')
            ->where('b.buyer is NULL')
            ->getQuery()
            ->getResult();
        foreach ( $querybuild as $item){
            dump($item);
            $order = $item->getOrderRef();
            $em->remove($item);
            $em->remove($order);
        }
        $em->flush();

        return $this->redirectToRoute('shop_reservation');
    }



}
