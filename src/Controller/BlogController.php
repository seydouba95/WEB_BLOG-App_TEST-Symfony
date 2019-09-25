<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     * @param ArticleRepository $repo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ArticleRepository $repo)
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/",name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new",name="blog_create")
     * @Route("/blog/{id}/edit",name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(ArticleType::class);
        if (!$article) {
            $article = new Article();
        }


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreateAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush(); //mene la requete vers Bdd
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}",name="blog_show")
     * @param ArticleRepository $repo
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(ArticleRepository $repo, $id)
    {

        $article = $repo->find($id);
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);

    }

}
