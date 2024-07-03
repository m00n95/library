<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/', name: 'app_book')]
    public function index(): Response
    {
        // Check if the user is authenticated
        if (!$this->getUser()) {
            // Redirect the user to the login page if not authenticated
            return $this->redirectToRoute('app_login');
        }

        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
}
