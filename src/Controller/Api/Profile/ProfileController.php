<?php

namespace App\Controller\Api\Profile;

use App\Entity\{Category, User, Visit};
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\{Core\Security};

/**
 * @Route("/api/profile", name="app_api_profile_profile_")
 */
class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security               $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/navigation", name="navigation", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function navigation()
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $userNavigation = [
            [
                'icon'      => 'fas fa-eye',
                'color'     => 'primary',
                'label'     => 'Monthly visits',
                'value'     => $this->entityManager->getRepository(Visit::class)->getVisitsCount($user,
                    new DateTime('first day of this month midnight'),
                    (new DateTime('last day of this month midnight'))->modify('+1 day -1 second')
                ),
                'path'      => '/visits',
                'component' => 'User/Visits'
            ],
            [
                'icon'      => 'fas fa-file-invoice',
                'color'     => 'success',
                'label'     => 'Billings',
                'value'     => $user->getBillings()->count(),
                'path'      => '/billings',
                'component' => 'User/Billings',
            ],
            [
                'icon'      => 'fas fa-info',
                'color'     => 'secondary',
                'label'     => 'Update my information',
                'value'     => null,
                'path'      => '/information',
                'component' => 'User/Information',
            ],
            [
                'icon'      => 'fas fa-address-book',
                'color'     => 'secondary',
                'label'     => 'Update my addresses',
                'value'     => $user->getAddresses()->count(),
                'path'      => '/addresses',
                'component' => 'User/Addresses',
            ],
        ];

        $adminNavigation = [];
        if ($user->isAdmin()) {
            $adminNavigation = [
                [
                    'icon'      => 'fas fa-list',
                    'color'     => 'success',
                    'label'     => 'Categories management',
                    'value'     => $this->entityManager->getRepository(Category::class)->count([]),
                    'path'      => '/categories',
                    'component' => 'Admin/CategoriesManagement/Main'
                ],
            ];
        }

        return new JsonResponse(['user' => $userNavigation, 'admin' => $adminNavigation]);
    }
}
