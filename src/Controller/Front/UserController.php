<?php

namespace App\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="app_front_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile()
    {
        return $this->render('user/profile.html.twig');
    }
}
