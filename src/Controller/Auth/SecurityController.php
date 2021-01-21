<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductReferenceRepository;
use App\Repository\UserRepository;
use App\Services\Shop\CartManagement\CartManagerInCookie;
use App\Services\Shop\CartManagement\CartManagerInDatabase;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route(name="app_auth_security_")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_front_home_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/usurp/{user}", name="usurp_show", requirements={"user":"\d+"}, methods={"GET"})
     * @param User $user
     */
    public function usurp(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        ProductReferenceRepository $productReferenceRepository,
        CartRepository $cartRepository,
        NormalizerInterface $normalizer
    ) {
        $cartManager = new CartManagerInDatabase($entityManager, $productReferenceRepository, $cartRepository, $user,
            $normalizer);
        if ($cartManager->merge(
            $request->cookies->has(CartManagerInCookie::COOKIE_KEY) ?
                unserialize($request->cookies->get(CartManagerInCookie::COOKIE_KEY)) : []
        )) {
            $entityManager->flush();
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        $response = new RedirectResponse($this->generateUrl('app_front_home_index'));
        $response->headers->clearCookie(CartManagerInCookie::COOKIE_KEY);
        return $response;
    }

    /**
     * @Route("/usurp", name="usurp_index", methods={"GET"})
     */
    public function usurpIndex(UserRepository $userRepository)
    {
        $users = $userRepository->findBy([], ['roles' => 'DESC']);
        return $this->render('auth/security/usurp.html.twig', compact('users'));
    }
}
