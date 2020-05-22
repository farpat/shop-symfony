<?php

namespace App\Services\Shop\Bank;


use App\Entity\Billing;
use App\Entity\Cart;
use App\Services\ModuleService;
use League\Flysystem\FilesystemInterface;
use mikehaertl\wkhtmlto\Pdf;
use Twig\Environment as Twig;

class BillingService
{
    private ModuleService       $moduleService;
    private FilesystemInterface $billingStorage;
    private Twig                $twig;

    public function __construct(ModuleService $moduleService, FilesystemInterface $billingStorage, Twig $twig)
    {
        $this->moduleService = $moduleService;
        $this->billingStorage = $billingStorage;
        $this->twig = $twig;
    }

    public function createBillingFromCart(Cart $cart): Billing
    {
        $lastNumberParameter = $this->moduleService->getParameter('billing', 'last_number');
        $currentNumber = $lastNumberParameter->getValue()['_value'] + 1;
        $lastNumberParameter->setValue(['_value' => $currentNumber]);

        return Billing::createFromCart($cart, $currentNumber);
    }

    public function generatePdf(Billing $billing): bool
    {
        $billingPdf = new Pdf();
        $billingPdf->addPage($this->twig->render('billing/show.html.twig', ['billing' => $billing]));
        return $billingPdf->saveAs($this->getPdfPath($billing));
    }

    public function getPdfPath(Billing $billing): string
    {
        return $this->billingStorage->getAdapter()->getPathPrefix() . $billing->getBillingPath();
    }

    public function isPdfExist(Billing $billing): bool
    {
        return $this->billingStorage->has($billing->getBillingPath());
    }
}