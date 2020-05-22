<?php

namespace App\Controller\Front;

use App\Entity\Cart;
use App\Entity\User;
use App\Services\ModuleService;
use App\Services\Shop\Bank\BillingService;
use App\Services\Shop\Bank\StripeService;
use App\Services\Shop\CartManagement\{CartManagerInCookie, CartManagerInDatabase, CartManagerInterface};
use App\Services\Support\Str;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Cookie, JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(name="app_cart_")
 */
class CartController extends AbstractController
{
    private CartManagerInterface   $cartManager;
    private SerializerInterface    $serializer;
    private EntityManagerInterface $entityManager;
    /**
     * @var Request
     */
    private Request $request;

    public function __construct(
        CartManagerInterface $cartManager,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->cartManager = $cartManager;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/cart-items", name="store_item", methods={"POST"})
     */
    public function storeItem(Request $request)
    {
        $orderItem = $this->cartManager->addItem(
            $request->request->getInt('quantity'),
            $productReferenceId = $request->request->getInt('productReferenceId')
        );

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($orderItem);
    }

    private function returnJsonResponseFromCartManager(array $orderItem): JsonResponse
    {
        $response = new JsonResponse($orderItem);

        if ($this->cartManager instanceof CartManagerInCookie) {
            $response->headers->setCookie(
                new Cookie($this->cartManager::COOKIE_KEY, serialize($this->cartManager->getPureItems()))
            );
        }

        return $response;
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="patch_item", methods={"PATCH"})
     */
    public function patchItem(int $productReferenceId, Request $request)
    {
        $orderItem = $this->cartManager->patchItem(
            $request->request->getInt('quantity'),
            $productReferenceId
        );

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($orderItem);
    }

    /**
     * @Route("/cart-items/{productReferenceId}", name="delete_item", methods={"DELETE"})
     */
    public function deleteItem(int $productReferenceId)
    {
        $orderItem = $this->cartManager->deleteItem($productReferenceId);

        if ($this->cartManager instanceof CartManagerInDatabase) {
            $this->entityManager->flush();
        }

        return $this->returnJsonResponseFromCartManager($orderItem);
    }

    /**
     * @Route("/purchase", name="purchase", methods={"GET"})
     */
    public function showPurchaseForm(
        TranslatorInterface $translator,
        CartManagerInterface $cartManager,
        StripeService $stripeService
    ) {
        /** @var User|null $user */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash('error',
                $translator->trans('To purchase your cart, you should {{ loginUrlStart }}login{{ urlEnd }} or {{ registerUrlStart }}create an account{{ urlEnd }}',
                    [
                        '{{ loginUrlStart }}'    => '<a href="' . $this->generateUrl('app_auth_security_login') . '">',
                        '{{ registerUrlStart }}' => '<a href="' . $this->generateUrl('app_auth_register') . '">',
                        '{{ urlEnd }}'           => '</a>'
                    ]));

            return $this->redirectToRoute('app_home_index');
        }

        if (empty($cartManager->getPureItems())) {
            $this->addFlash('error', $translator->trans('You must have items in your cart to purchase something'));

            return $this->redirectToRoute('app_home_index');
        }


        return $this->render('cart/purchase.html.twig', [
            'stripePublicKey'      => $stripeService->getPublicKey(),
            'successfulPaymentUrl' => $this->generateUrl('app_cart_purchase_successful_payment')
        ]);
    }

    /**
     * @Route("/purchase/create-intent", name="purchase_create_intent", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function createIntent(StripeService $stripeService, CartManagerInterface $cartManager, Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();
        $address = $user->getAddresses()[0];

        return new JsonResponse([
            'customer_name'            => $user->getUsername(),
            'customer_billing_address' => [
                'line1'       => $address->getLine1(),
                'line2'       => $address->getLine2(),
                'postal_code' => $address->getPostalCode(),
                'city'        => $address->getCity(),
                'country'     => $address->getCountryCode(),
            ],
            'client_secret'            => $stripeService->createIntent($cartManager->getCart())->client_secret
        ]);
    }

    /**
     * @Route("/purchase/webhook-stripe", name="purchase_webhook_stripe")
     */
    public function webhookSuccessfulPayment(
        StripeService $stripeService,
        Request $request,
        EntityManagerInterface $entityManager,
        BillingService $billingService
    ) {
        $event = $stripeService->handleRequest($request);

        switch ($event->type) {
            case Event::PAYMENT_INTENT_SUCCEEDED:
                $paymentIntent = $event->data->object;

                /** @var Cart $cart */
                $cart = $entityManager->getRepository(Cart::class)->findOneBy(['webhookPaymentId' => $paymentIntent->id]);

                if ($cart === null) {
                    throw new NotFoundHttpException("Cart corresponding to paymentId not found!");
                }

                $entityManager->persist($billing = $billingService->createBillingFromCart($cart));
                $entityManager->remove($cart);
                $entityManager->flush();

                $billingService->generatePdf($billing);
                break;
            default:
                return new Response('', 404);
        }

        return new Response();
    }

    /**
     * @Route("/purchase/successful-payment/{paymentId?}", name="purchase_successful_payment", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function redirectSuccessfulPayment(
        TranslatorInterface $translator,
        CartManagerInterface $cartManager,
        ModuleService $moduleService,
        EntityManagerInterface $entityManager,
        string $paymentId
    ) {
        /** @var Cart $cart */
        $cart = $cartManager->getCart();
        $cart->setWebhookPaymentId($paymentId);

        $entityManager->flush();

        $this->addFlash('success',
            $translator->trans('Great! You paid {{ totalIncludingTaxes }} with success. You will receive an email containing the corresponding billing',
                [
                    '{{ totalIncludingTaxes }}' => Str::getFormattedPrice(
                        $moduleService->getParameter('billing', 'currency')->getValue()['code'],
                        $cart->getTotalAmountIncludingTaxes()
                    )
                ]
            ));


        return $this->redirectToRoute('app_home_index');
    }
}
