<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
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
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(["user_recipes" => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Security("recipe.isIsPublic() === true || user === recipe.getUserRecipes()")]
    #[Route('/recette/vues/{id}', 'recipe.show', methods: ['GET', 'POST'])]
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, RecipeRepository $recipeRepository, EntityManagerInterface $manager): Response
    {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $mark->setUserMark($this->getUser())
                ->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy([
                'userMark' => $this->getUser(),
                'recipe' => $recipe
            ]);

            $isUserRecipe = $recipeRepository->findOneBy([
                'user_recipes' => $this->getUser()->getId(),
            ]);

            if($isUserRecipe){
                $this->addFlash(
                    'danger',
                    'Vous ne pouvez pas noter votre propre recette'
                );
                return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
            }

            if(!$existingMark){
                $manager->persist($mark);
            }else{
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été prise en compte'
            );

            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recette/publique', 'recipe.index.public', methods: ['GET'])]
    public function indexPublic(RecipeRepository $repository, PaginatorInterface $paginator, Request $request) : Response
    {
        $recipes = $paginator->paginate(
            $repository->findPublicRecipes(null),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe//index_public.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            $recipe->setUserRecipes($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créée avec succès'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', ['form' => $form->createView()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Security("user === recipe.getUserRecipes()")]
    #[Route('/recette/edition/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', ["form" => $form->createView()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Security("user === recipe.getUserRecipes()")]
    #[Route('recette/suppression/{id}', name: 'recipe.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, ?Recipe $recipe): Response
    {
        if(!$recipe){
            $this->addFlash(
                'error',
                'Votre recette n\'a pas été trouvée'
            );
            return $this->redirectToRoute('recipe.index');
        }
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
