<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkFormType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(PaginatorInterface $paginator, Request $request, RecipeRepository $recipRepository): Response
    {
        /** IsGranted is the alternative way */
        /* if(!$this->getUser()) {
            return $this->redirectToRoute('home.index');
        }
        */
        $recipes = $paginator->paginate(
            /* $recipRepository->findAll(), */
            $recipRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), 8
        );

        return $this->render('pages/recipe/index.html.twig', ['recipes' => $recipes,]);
    }

    #[IsGranted('ROLE_USER')]
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
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
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

    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recipe/delete/{id}', 'recipe_delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe) : Response
    {
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash('success', 'Recipe successfully deleted !');
        return $this->redirectToRoute('recipe_index');
    }

    #[Route('/recipe/show/{id}', 'recipe_show')]
    #[Security("is_granted('ROLE_USER') and recipe.getIsPublic() === true")]
    public function show(Recipe $recipe, Request $request, EntityManagerInterface $emanager, MarkRepository $markRepository)
    {
        /** Mark in order to recupère the current user & the recipe en question - ready to be save in DB */
        $mark = new Mark();
        $form = $this->createForm(MarkFormType::class, $mark);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser())->setRecipe($recipe);
            
            /** check if the user had already mark this recipe */
            $existingMark = $markRepository->findOneBy([
                'user'   => $this->getUser(),
                'recipe' => $recipe
            ]);

            if(!$existingMark) {
                $emanager->persist($mark);
                //$this->addFlash('success', 'Votre note a bien été prise en compte.');
            }else {
                //dd($existingMark);
                //$this->addFlash('success', 'Désolé, vous avez déjà noté cette recette.');
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }
            $emanager->flush();

            $this->addFlash('success', 'Votre note a bien été prise en compte.');
            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);

        }

        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe, 'form' => $form->createView()
        ]);
    }

    #[Route('/recipe/public', 'recipe_public')]
    public function recipePublic(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request)
    {
        /* $recipes = $recipeRepository->findAll(); */
        $recipes = $paginator->paginate($recipeRepository->findPublicRecipe(null), $request->query->getInt('page', 1), 10);
        return $this->render('pages/recipe/public.html.twig', [
            'recipes' => $recipes
        ]);
    }

}
