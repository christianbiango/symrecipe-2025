<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegitrationType;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted('ROLE_USER')]
#[Security("user === choosenUser")]
class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit')]
    public function edit(User $choosenUser, EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class, $choosenUser);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            if($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {
                $user = $form->getData();

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    "success",
                    "Votre compte a été modifié avec succès"
                );
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig',
            ["form" => $form->createView()],
        );
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                //IMPORTANT: For the prePersist entity listener method to work, we have to change at least one table column. Before, plain password wasn't one, thus the error.
                $choosenUser->setUpdatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash(
                    "success",
                    "Le mot de passe a été mis à jour"
                );

                return $this->redirectToRoute("recipe.index");
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
