<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;
use App\Form\CreateNewsFormType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class NewsController extends AbstractController
{
    /**Création d'une page de vue des news**/

    /**
     * @Route("/news", name="news")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function news(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $newrepo = $em->getRepository(News::class);
        $news = $newrepo->findAll();
        return $this->render('news/index.html.twig', [
        'news'=>$news
        ]);
    }

    /**Création d'une interface de création de news avec formulaire**/

    /**
     * @Route("/newsroom", name="create_News")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     */
    public function createNews(Request $request): Response
    {
        $newNews = new News();
        $form = $this->createForm(CreateNewsFormType::class,$newNews);
        //appel de la bdd pour remplir les news
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
                $newNews->setAuthor($this->getUser());

            //sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($newNews);
            $em->flush();
            //Message flash de succès
            $this->addFlash('success','Faites tourner les rotatives !');
            //redirection sur la page vue
            return $this->redirectToRoute('news');
        }
        //envoi du formulaire général
        return $this->render('news/createNews.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //page de vue d'une news en détail.
    /**
     * @Route("/news/{slug}/",  name="view_article")
     */
    public function viewNews(News $news): Response
    {
    return $this-> render('news/viewNews.html.twig',[
        ]);
    }


    //supression d'une news
    /**
     * Page admin servant à supprimer un article via son id passé dans l'URL
     *
     * @Route("/publication/suppression/{id}/", name="publication_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function publicationDelete(News $news, Request $request): Response
{

    if(!$this->isCsrfTokenValid('publication_delete_' . $news->getId(), $request->query->get('csrf_token'))){

        $this->addFlash('error', 'Token sécurité invalide, veuillez ré-essayer.');
    } else {

        // Manager général
        $em = $this->getDoctrine()->getManager();

        // Suppression de l'article
        $em->remove($news);
        $em->flush();

        // Message flash de succès + redirection sur la liste des articles
        $this->addFlash('success', 'La publication a été supprimée avec succès !');
    }


    return $this->redirectToRoute('news');

} }
