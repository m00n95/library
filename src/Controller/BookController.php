<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        // Check if the user is authenticated
        if (!$this->getUser()) {
            // Redirect the user to the login page if not authenticated
            return $this->redirectToRoute('app_login');
        }

        // Get the currently authenticated user
        $user = $this->getUser();
        // Find books associated with the current user
        $books = $bookRepository->findBy(['user' => $user]);

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/add', name:'app_book_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the creation date of the book to the current time
            $book->setCreatedAt(new \DateTime('now' , new \DateTimeZone('Europe/Paris')));
            // Associate the book with the current user
            $book->setUser($this->getUser());

            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/add.html.twig', [
            'book'=> $book,
            'form'=> $form,
        ]);
    }

    #[Route('/delete/{id}', name:'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $em->remove($book);
            $em->flush();
        }

        return $this->redirectToRoute('app_book_index');
    }
}
