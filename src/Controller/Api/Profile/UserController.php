<?php

namespace App\Controller\Api\Profile;

use App\Entity\{Product, User, Visit};
use App\FormData\{UpdateMyAddressesFormData, UpdateMyInformationsFormData};
use App\Repository\VisitRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\{Core\Security};
use Symfony\Component\Serializer\{Exception\ExceptionInterface, Normalizer\NormalizerInterface};
use Symfony\Component\Validator\{ConstraintViolation, Validator\ValidatorInterface};

/**
 * @Route("/api/profile/user", name="app_api_profile_user_")
 */
class UserController extends AbstractController
{
    private NormalizerInterface    $normalizer;
    private ValidatorInterface     $validator;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface  $parameterBag;
    private Security               $security;

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
     * @Route("/me", name="me", methods={"GET", "PUT"})
     * @IsGranted("ROLE_USER")
     * @throws ExceptionInterface
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
     * @throws ExceptionInterface
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

    /**
     * @Route("/billings", name="billings", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @throws ExceptionInterface
     */
    public function billings()
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return new JsonResponse($this->normalizer->normalize($user->getBillings(), 'billings-json'));
    }

    /**
     * @Route("/visits", name="visits", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @throws ExceptionInterface
     */
    public function visits(VisitRepository $visitRepository)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $visits = $visitRepository->getVisits($user,
            new DateTime('first day of this month midnight'),
            (new DateTime('last day of this month midnight'))->modify('+1 day -1 second')
        );

        return new JsonResponse($this->normalizer->normalize($visits, 'billings-json'));
    }
}
