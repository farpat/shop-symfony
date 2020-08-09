<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\FormData\UpdateMyAddressesFormData;
use App\FormData\UpdateMyInformationsFormData;
use App\Repository\VisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\{Core\Security};
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\{ConstraintViolation, Validator\ValidatorInterface};

/**
 * @Route("/profile-api", name="app_profileapi_user_")
 */
class ProfileApiController extends AbstractController
{
    private NormalizerInterface    $normalizer;
    private ValidatorInterface     $validator;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface  $parameterBag;
    /**
     * @var Security
     */
    private Security $security;

    public function __construct(
        Security $security,
        NormalizerInterface $normalizer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag
    ) {
        $this->normalizer = $normalizer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
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

        $navigations = [
            [
                'path'      => '/',
                'component' => 'ViewMyStatistics',
                'label'     => 'View my statistics'
            ],
            [
                'path'      => '/view-my-billings',
                'component' => 'ViewMyBillings',
                'label'     => 'View my billings'
            ],
            [
                'path'      => '/update-my-information',
                'component' => 'UpdateMyInformation',
                'label'     => 'Update my information'
            ],
            [
                'path'      => '/update-my-addresses',
                'component' => 'UpdateMyAddresses',
                'label'     => 'Update my addresses'
            ],
        ];

        if ($user->isAdmin()) {

        }

        return new JsonResponse($navigations);
    }

    /**
     * @Route("/me", name="me", methods={"GET", "PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function me(Request $request)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($request->getMethod() === 'PUT') {
            $formData = new UpdateMyInformationsFormData(array_merge(
                $request->request->all(),
                ['id' => $user->getId()]
            ));

            $errors = $this->getErrors($formData);

            if (count($errors) > 0) {
                return new JsonResponse($errors, 422);
            } else {
                $formData->updateUser($user);
                $this->entityManager->flush();
            }
        }

        return new JsonResponse($this->normalizer->normalize($user, 'json'));
    }

    private function getErrors($formData): array
    {
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validate($formData);

        if (count($errors) === 0) {
            return [];
        }

        $errorResponse = [];
        foreach ($errors as $error) {
            $explodedPropertyPath = explode('.', $error->getPropertyPath());

            if (count($explodedPropertyPath) === 1) {
                $errorResponse[$error->getPropertyPath()] = $error->getMessage();
            } else {
                $errorResponse[$explodedPropertyPath[0]] = $errorResponse[$explodedPropertyPath[0]] ?? [];
                $errorResponse[$explodedPropertyPath[0]][$explodedPropertyPath[1]] = $error->getMessage();
            }

        }

        return $errorResponse;
    }

    /**
     * @Route("/addresses", name="addresses", methods={"GET", "PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function addresses(Request $request, EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($request->getMethod() === 'PUT') {
            $formData = new UpdateMyAddressesFormData(
                $request->request->all(),
                $this->parameterBag->get('GOOGLE_GEOCODE_KEY'),
                $entityManager
            );

            $errors = $this->getErrors($formData);

            if (count($errors) > 0) {
                return new JsonResponse($errors, 422);
            } else {
                $formData->updateUser($user);
                $this->entityManager->flush();
            }
        }
        return new JsonResponse(array_merge(
            $this->normalizer->normalize($user, 'addresses-json'),
            [
                'algolia' => [
                    'id'  => $this->parameterBag->get('ALGOLIA_API_ID'),
                    'key' => $this->parameterBag->get('ALGOLIA_API_KEY')
                ]
            ]
        ));
    }

    /** @Route("/billings", name="billings", methods={"GET"}) */
    public function billings()
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return new JsonResponse($this->normalizer->normalize($user->getBillings(), 'billings-json'));
    }

    /** @Route("/statistics", name="statistics", methods={"GET"}) */
    public function statistics(VisitRepository $visitRepository)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $visits = $visitRepository->getVisits($user,
            new \DateTime('first day of this month midnight'),
            (new \DateTime('last day of this month midnight'))->modify('+1 day -1 second')
        );

        $statistics = [
            [
                'icon'  => 'eye',
                'color' => 'secondary',
                'label' => 'Monthly visits',
                'value' => count($visits)
            ],
            [
                'icon'  => 'file-invoice',
                'color' => 'primary',
                'label' => 'Billings',
                'value' => $user->getBillings()->count()
            ]
        ];

        return new JsonResponse($statistics);
    }
}
