<?php

namespace App\Services\Shop\Bank;


use App\Entity\Cart;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Stripe;

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

    public function createIntent(Cart $cart)
    {
        $this->setApiKey();

        return PaymentIntent::create([
            'amount'   => $cart->getTotalAmountIncludingTaxes() * 100,
            'currency' => $this->currency,
            'metadata' => ['integration_check' => 'accept_a_payment']
        ]);
    }

    private function setApiKey()
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

    public function handleRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->setApiKey();

        return Event::constructFrom(
            json_decode($request->getContent(), true)
        );
    }


}