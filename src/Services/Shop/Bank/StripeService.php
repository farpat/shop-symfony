<?php

namespace App\Services\Shop\Bank;


use App\Entity\Cart;
use Stripe\{Event, PaymentIntent, Stripe};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StripeService
{

    private string $publicKey;
    private string $secretKey;
    private string $currency;

    private bool $isPrivateKeySetted = false;

    public function __construct(string $publicKey, string $secretKey, string $currency)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        $this->currency = $currency;

    }

    public function createIntent(Cart $cart): PaymentIntent
    {
        $this->setApiKey();

        return PaymentIntent::create([
            'amount'   => (float)$cart->getTotalAmountIncludingTaxes() * 100,
            'currency' => $this->currency,
            'metadata' => ['integration_check' => 'accept_a_payment']
        ]);
    }

    private function setApiKey(): void
    {
        if (!$this->isPrivateKeySetted) {
            Stripe::setApiKey($this->secretKey);
            $this->isPrivateKeySetted = true;
        }
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function handleRequest(Request $request): Event
    {
        $this->setApiKey();

        $content = $request->getContent();

        if ($content === '') {
            throw new BadRequestHttpException('Content is empty');
        }

        return Event::constructFrom(
            json_decode($content, true)
        );
    }


}