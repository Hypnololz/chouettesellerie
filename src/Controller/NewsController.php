<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    public function news(PaginatorInterface $paginator, Request $request): Response
    {
        $requestedPage = $request->query->getInt('page', 1);
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT a FROM App\Entity\News a');

        $pageNews = $paginator->paginate(
            $query,
            $requestedPage,
            16
        );

        return $this->render('news/index.html.twig', [
        'news'=>$pageNews
        ]);
    }

    /**Interface de création de news avec formulaire**/

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
            $photo = $form->get('photo')->getData();

            //génération du nom de photo pour stockage
            do{
                $newPhotoName = md5(random_bytes(100)). '.' . $photo->guessExtension();
            } while(file_exists('img/news' .$newPhotoName));
            $newNews->setPhoto($newPhotoName);

            //sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($newNews);
            $em->flush();

            //Uploader la photo dans le dossier
            $photo->move(
                'img/news',
                $newPhotoName
            );
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
     * @Route("/news/{slug}/",  name="view_news")
     */
    public function viewNews(News $news): Response
    {
        return $this-> render('news/viewNews.html.twig',[
            "news"=>$news
        ]);
    }


    //supression d'une news
     //Page admin servant à supprimer un article via son id passé dans l'URL
    /**
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
