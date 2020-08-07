<?php

namespace App\Controller\Front;

use App\Entity\{Cart, User};
use App\Services\{ModuleService,
    Shop\Bank\BillingService,
    Shop\Bank\StripeService,
    Shop\CartManagement\CartManagerInterface,
    Support\Str
};
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/purchase", name="app_front_purchase_")
 */
class PurchaseController extends AbstractController
{
    private StripeService          $stripeService;
    private CartManagerInterface   $cartManager;
    private EntityManagerInterface $entityManager;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(
        StripeService $stripeService,
        CartManagerInterface $cartManager,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->stripeService = $stripeService;
        $this->cartManager = $cartManager;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="purchase", methods={"GET"})
     */
    public function showPurchaseForm()
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash('error',
                $this->translator->trans('To purchase your cart, you should {{ loginUrlStart }}login{{ urlEnd }} or {{ registerUrlStart }}create an account{{ urlEnd }}',
                    [
                        '{{ loginUrlStart }}'    => '<a href="' . $this->generateUrl('app_auth_security_login') . '">',
                        '{{ registerUrlStart }}' => '<a href="' . $this->generateUrl('app_auth_register') . '">',
                        '{{ urlEnd }}'           => '</a>'
                    ]));

            return $this->redirectToRoute('app_front_home_index');
        }

        if (empty($this->cartManager->getPureItems())) {
            $this->addFlash('error',
                $this->translator->trans('You must have items in your cart to purchase something'));

            return $this->redirectToRoute('app_front_home_index');
        }

        $url = $this->generateUrl('app_front_purchase_successful_payment', ['paymentId' => 'paymentId']);

        return $this->render('purchase/purchase.html.twig', [
            'stripePublicKey'      => $this->stripeService->getPublicKey(),
            'successfulPaymentUrl' => str_replace('/paymentId', '', $url)
        ]);
    }

    /**
     * @Route("/create-intent", name="create_intent", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function createIntent()
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse([
            'customer_name'            => $user->getUsername(),
            'customer_billing_address' => [
                'line1'       => $user->getDeliveryAddress()->getLine1(),
                'line2'       => $user->getDeliveryAddress()->getLine2(),
                'postal_code' => $user->getDeliveryAddress()->getPostalCode(),
                'city'        => $user->getDeliveryAddress()->getCity(),
                'country'     => $user->getDeliveryAddress()->getCountryCode(),
            ],
            'client_secret'            => $this->stripeService->createIntent($this->cartManager->getCart())->client_secret
        ]);
    }

    /**
     * @Route("/webhook-stripe", name="webhook_stripe")
     */
    public function webhookSuccessfulPayment(Request $request, BillingService $billingService)
    {
        $event = $this->stripeService->handleRequest($request);

        switch ($event->type) {
            case Event::PAYMENT_INTENT_SUCCEEDED:
                $paymentIntent = $event->data->object;

                /** @var Cart $cart */
                $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['webhookPaymentId' => $paymentIntent->id]);

                if ($cart === null) {
                    throw new NotFoundHttpException("Cart corresponding to paymentId not found!");
                }

                $this->entityManager->persist($billing = $billingService->createBillingFromCart($cart));
                $this->entityManager->remove($cart);
                $this->entityManager->flush();

                $billingService->generatePdf($billing);

                break;
            default:
                return new Response('', 404);
        }

        return new Response();
    }

    /**
     * @Route("successful-payment/{paymentId}", name="successful_payment", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function redirectSuccessfulPayment(ModuleService $moduleService, string $paymentId)
    {
        /** @var Cart $cart */
        $cart = $this->cartManager->getCart();
        $cart->setWebhookPaymentId($paymentId);

        $this->entityManager->flush();

        //TODO: write an email

        $this->addFlash('success',
            $this->translator->trans('Great! You paid {{ totalIncludingTaxes }} with success. You will receive an email containing the corresponding billing',
                [
                    '{{ totalIncludingTaxes }}' => Str::getFormattedPrice(
                        $moduleService->getParameter('billing', 'currency')->getValue(),
                        $cart->getTotalAmountIncludingTaxes()
                    )
                ]
            ));


        return $this->redirectToRoute('app_front_home_index');
    }
}
