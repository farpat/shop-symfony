<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\Form\UpdateMyInformationsType;
use App\FormData\UpdateMyInformationsFormData;
use Doctrine\ORM\EntityManagerInterface;
use Psy\Util\Json;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    /** @var UserInterface|User|null */
    private UserInterface       $user;
    private NormalizerInterface $normalizer;
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(
        Security $security,
        NormalizerInterface $normalizer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ) {
        $this->user = $security->getUser();
        $this->normalizer = $normalizer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/me", name="me", methods={"GET", "PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function me(Request $request)
    {
        if ($request->getMethod() === 'PUT') {
            $formData = new UpdateMyInformationsFormData($request->request->all());
            $formData->setId($this->user->getId());

            /** @var ConstraintViolation[] $errors */
            $errors = $this->validator->validate($formData);

            if (count($errors) > 0) {
                $erorrResponse = [];
                foreach ($errors as $error) {
                    $erorrResponse[$error->getPropertyPath()] = $error->getMessage();
                }

                return new JsonResponse($erorrResponse, 422);
            } else {
                $formData->updateUser($this->user);
                $this->entityManager->flush();
            }
        }

        return new JsonResponse($this->normalizer->normalize($this->user, 'json'));
    }

    public function address(Request $request)
    {
        return new JsonResponse($this->normalizer->normalize($this->user, 'addresses'));
    }
}