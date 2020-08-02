<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\Form\UpdateMyInformationsType;
use App\FormData\UpdateMyAddressesFormData;
use App\FormData\UpdateMyInformationsFormData;
use Doctrine\ORM\EntityManagerInterface;
use Farpat\Api\Api;
use Psy\Util\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/profile-api", name="app_profileapi_user_")
 */
class ProfileApiController extends AbstractController
{
    /** @var UserInterface|User $user */
    private UserInterface          $user;
    private NormalizerInterface    $normalizer;
    private ValidatorInterface     $validator;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface  $parameterBag;

    public function __construct(
        Security $security,
        NormalizerInterface $normalizer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag
    ) {
        $this->user = $security->getUser();
        $this->normalizer = $normalizer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/me", name="me", methods={"GET", "PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function me(Request $request)
    {
        if ($request->getMethod() === 'PUT') {
            $formData = new UpdateMyInformationsFormData(array_merge(
                $request->request->all(),
                ['id' => $this->user->getId()]
            ));

            $errors = $this->getErrors($formData);

            if (count($errors) > 0) {
                return new JsonResponse($errors, 422);
            } else {
                $formData->updateUser($this->user);
                $this->entityManager->flush();
            }
        }

        return new JsonResponse($this->normalizer->normalize($this->user, 'json'));
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
    public function addresses(Request $request)
    {
        if ($request->getMethod() === 'PUT') {
            $formData = new UpdateMyAddressesFormData(
                $request->request->all(),
                $this->parameterBag->get('GOOGLE_GEOCODE_KEY')
            );

            $errors = $this->getErrors($formData);

            if (count($errors) > 0) {
                return new JsonResponse($errors, 422);
            } else {
                $formData->updateUser($this->user);
                $this->entityManager->flush();
            }
        }
        return new JsonResponse(array_merge(
            $this->normalizer->normalize($this->user, 'addresses'),
            [
                'algolia' => [
                    'id'  => $this->parameterBag->get('ALGOLIA_API_ID'),
                    'key' => $this->parameterBag->get('ALGOLIA_API_KEY')
                ]
            ]
        ));
    }
}
