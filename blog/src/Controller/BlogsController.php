<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\User;
use App\Form\BlogFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class BlogsController extends AbstractController
{
    private $userId = 1;

   

    /**
     * @Route("/blogs", name="app_blogs")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $blog = $doctrine->getRepository(Blog::class)->findAll();
        //dd($blog);
        return $this->render("index.html.twig", [
            'blogs'=> $blog
        ]);
    }


    /**
     * @Route("/blogs/{id}", name="app_blog")
     */
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        $blog = $doctrine->getRepository(Blog::class)->find($id);
        //dd($blog);
        return $this->render("show.html.twig", [
            'blog'=>$blog
        ]);
    }


    /**
     * @Route("/blog/create", name="create_blog")
     */
    public function createBlog(ManagerRegistry $doctrine, Request $request): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogFormType::class, $blog);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newBlog = $form->getData();
            $newBlog->setDate(new \DateTime());
            $user = $doctrine->getRepository(User::class)->find($this->userId);
            $newBlog->setUser($user);

            $entityManager = $doctrine->getManager();

            $entityManager->persist($newBlog);
            $entityManager->flush();

            return $this->redirectToRoute('app_blogs');
        }

        return $this->render("create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="update_blog")
     */
    public function updateBlog(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $blog = $doctrine->getRepository(Blog::class)->find($id);
        $form = $this->createForm(BlogFormType::class, $blog);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setTitle($form->get('title')->getData());
            $blog->setContent($form->get('content')->getData());

            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('app_blogs');
        }

        return $this->render("update.html.twig", [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/delete/{id}", methods={"GET", "DELETE"}, name="delete_blog")
     */
    public function deleteBlog(ManagerRegistry $doctrine, $id): Response
    {
        $blog = $doctrine->getRepository(Blog::class)->find($id);
        
        $entityManager = $doctrine->getManager();

        $entityManager->remove($blog);
        $entityManager->flush();

        return $this->redirectToRoute('app_blogs');
    }

    
}
