<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe_index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request, RecipeRepository $recipRepository): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('home.index');
        }

        $recipes = $paginator->paginate(
            /* $recipRepository->findAll(), */
            $recipRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), 8
        );

        return $this->render('pages/recipe/index.html.twig', ['recipes' => $recipes,]);
    }

    #[Route('/recipe/new', 'recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        
        // Processing form
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success', 'Recipe successfully saved !');

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('pages/recipe/new.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/recipe/edit/{id}', 'recipe_edit', methods: ['GET', 'POST'])]
    public function edit(EntityManagerInterface $manager, Recipe $recipe, Request $request) : Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash('success', 'Recipe successfully edited !');
            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('pages/recipe/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/recipe/delete/{id}', 'recipe_delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe) : Response
    {
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash('success', 'Recipe successfully deleted !');
        return $this->redirectToRoute('recipe_index');
    }

}
