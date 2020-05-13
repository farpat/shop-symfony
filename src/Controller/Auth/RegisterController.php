<?php

namespace App\Controller\Auth;

use App\Form\RegisterFormType;
use App\FormData\RegisterFormData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_auth_register", methods={"GET", "POST"})
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator
    ): Response {
        $data = new RegisterFormData();
        $form = $this->createForm(RegisterFormType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user = $data->makeUser($passwordEncoder);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->addFlash('success',
                $translator->trans('Your account is created with success! You received an email to confirm it.'));


            return $this->redirectToRoute('app_home_index');
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
