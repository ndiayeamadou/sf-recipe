<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileFormType;
use App\Form\UserPwdFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'profile')]
    public function index() {
        /* if(!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour pouvoir accéder à cette page.');
            return $this->redirectToRoute('security_login');
        }
         */
        return $this->render('pages/user/profile.html.twig');
    }

    #[Route('/edit-user/{id}', name: 'edit')]
    #[Security("is_granted('ROLE_USER') and user === userEntity")]
    public function edit(User $userEntity, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        /* if($this->getUser() == null) { */
        /* if(!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('home.index');
        }
        if($userEntity != $this->getUser()) {
            $this->addFlash('warning', 'This user does not exist in our records');
            return $this->redirectToRoute('home.index');
        }
        */
        $form = $this->createForm(UserProfileFormType::class, $userEntity);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** if we wanna add pwd field & check if it is valid */
            if($passwordHasher->isPasswordValid($userEntity, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Votre profil a bien été modifié.');
                return $this->redirectToRoute('user_profile');
            }else {
                $this->addFlash('warning', 'Veuillez saisir le bon mot de passe pour poursuivre l\'opération');
                return $this->redirectToRoute('user_profile');
            }
        }

        return $this->render('pages/user/edit_user.html.twig', [
            'formUserEdit' => $form->createView(),
        ]);
    }

    #[Security("is_granted('ROLE_USER') and user === userEntity")]
    #[Route('/edit-password/{userEntity}', 'pwd_edit')]
    public function editPwd(
        User $userEntity, Request $request, UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $emanager
    ) {
        if(!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour pouvoir accéder à cette page.');
            return $this->redirectToRoute('security_login');
        }
        if($this->getUser() != $userEntity) {
            $this->addFlash('warning', 'This user does not exist in our records');
            return $this->redirectToRoute('home.index');
        }

        $form = $this->createForm(UserPwdFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            if($passwordHasher->isPasswordValid($userEntity, $form->getData()['plainPassword'])) {
                //dd($form->getData());
                $userEntity->setPassword(
                    $passwordHasher->hashPassword(
                        $userEntity,
                        $form->getData()['newPassword']
                    )
                );
                $emanager->persist($userEntity);
                $emanager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été changé.');
                return $this->redirectToRoute('user_profile');
            }else {
                $this->addFlash('warning', 'Votre ancien mot de passe n\'est pas valide.');
                return $this->redirectToRoute('user_profile');
            }
        }
        return $this->render('pages/user/edit_pwd.html.twig', [
            'form' => $form->CreateView()
        ]);
    }
}
