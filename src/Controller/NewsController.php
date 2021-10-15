<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;
use App\Form\CreateNewsFormType;

class NewsController extends AbstractController
{
    /**Création d'une page de vue des news
     * TODO: limiter la vue au seul admin
     **/

    /**
     * @Route("/news", name="news")
     */
    public function news(): Response
    {
        return $this->render('news/index.html.twig', [

        ]);
    }
    /**Création d'une interface de création de news avec formulaire
     * TODO: limiter la vue au seul admin
     **/

    /**
     * @Route("/newsroom", name="create_News")
     */
    public function createNews(): Response
    {
        $newNews = new News();
        $form = $this->createForm(CreateNewsFormType::class,$newNews);
        return $this->render('news/createNews.html.twig', [
            'form' => $form->createView()

        ]);
    }
}
