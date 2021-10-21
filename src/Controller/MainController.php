<?php

namespace App\Controller;

use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $newrepo = $em->getRepository(News::class);
        $news = $newrepo->findBy([],['datePublication'=>'DESC'],2);
        return $this->render('main/index.html.twig',[
            'news' => $news
        ]);
    }
    /**
     * @Route("/nous_connaitre/", name="nous_connaitre")
     */
    public function nousConnaitre(): Response
    {
        return $this->render('main/nous_connaitre.html.twig');
    }
    /**
     * @Route("/mentions-legales/", name="mentions_legales")
     */
    public function mentionsLegales(): Response
    {
        return $this->render('main/mentions_legales.html.twig');
    }
    /**
     * @Route("/cgu/", name="cgu")
     */
    public function cgu(): Response
    {
        return $this->render('main/cgu.html.twig');
    }


}
