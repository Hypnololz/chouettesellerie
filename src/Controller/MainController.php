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
        $news = $newrepo->findAll();
        return $this->render('main/index.html.twig',[
            'news' => $news
        ]);
    }

}
