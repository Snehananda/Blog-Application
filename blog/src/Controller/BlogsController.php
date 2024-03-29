<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\User;
use App\Form\BlogFormType;
use App\Form\UserLoginFormType;
use App\Form\UserRegisterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class BlogsController extends AbstractController
{
    private $userId = 1;


    /**
     * @Route("/", name="app_home")
     */
    public function home(): Response
    {
        return $this->render("home.html.twig");
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function register(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegisterFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newEmail = $form->get('email')->getData();
            $getUser = $doctrine->getRepository(User::class)->findOneBy(['email' => $newEmail]);
            
            if($getUser != null) {
                return $this->redirectToRoute('app_login');
            }

            $newUser = $form->getData();

            $entityManager = $doctrine->getManager();

            $entityManager->persist($newUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_blogs');
        }

        return $this->render("register.html.twig", [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserLoginFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();
            $password = $form->get('password')->getData();
            $getUser = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if($getUser == null || $getUser->getPassword() != $password) {
                return $this->redirectToRoute('app_login');
            }
            

            return $this->redirectToRoute('app_blogs');
        }

        return $this->render("login.html.twig", [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): Response
    {
        return $this->redirectToRoute('app_home');
    
    }


    /**
     * @Route("/blogs", name="app_blogs")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $blog = $doctrine->getRepository(Blog::class)->findAll();
        
        return $this->render("index.html.twig", [
            'blogs'=> $blog
        ]);
    }


    /**
     * @Route("/blog/{id}", name="app_blog")
     */
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        $blog = $doctrine->getRepository(Blog::class)->find($id);

        return $this->render("show.html.twig", [
            'blog'=>$blog
        ]);
    }


    /**
     * @Route("/blogs/create", name="app_create_blog")
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
     * @Route("/blog/update/{id}", name="app_update_blog")
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
     * @Route("/blog/delete/{id}", name="app_delete_blog")
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
