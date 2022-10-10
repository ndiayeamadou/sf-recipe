<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $ingrRepository,
    PaginatorInterface $paginator, Request $request): Response
    {
        //$ingredients = $ingrRepository->findAll();
        $ingredients = $paginator->paginate(
            $ingrRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        //dd($ingredients);

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/new', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(IngredientType::class, $ingredient);

        # Processing form
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Successfully inserted!'
            );
            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /** 2 optional Edit metods | 1st one: Using ParamConverter */
    #[Route('/ingredient/edit/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Modification réussie'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/edit2/{id}', 'ingredient.edit2', methods: ['GET', 'POST'])]
    public function edit2(Request $request, int $id, IngredientRepository $ingrRepository,
        EntityManagerInterface $manager) : Response
    {
        $ingredient = $ingrRepository->findOneBy(['id' => $id]);
        //dd($ingredient);

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Modification réussie'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/delete/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {
        $manager->remove($ingredient);
        $manager->flush();
        
        $this->addFlash(
            'success',
            'Ingredient deleted successfully !'
        );

        return $this->redirectToRoute('app_ingredient');
    }
    
}
