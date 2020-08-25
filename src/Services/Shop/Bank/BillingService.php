<?php

namespace App\Services\Shop\Bank;


use App\Entity\Billing;
use App\Entity\Cart;
use App\Services\ModuleService;
use League\Flysystem\FilesystemInterface;
use mikehaertl\wkhtmlto\Pdf;
use Twig\Environment as Twig;
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};

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

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generatePdf(Billing $billing): string
    {
        $billingPdf = new Pdf();
        $billingPdf->addPage($page = $this->twig->render('billing/show.html.twig', ['billing' => $billing]));
        $pdfPath = $this->getRealPdfPath($billing);
        $folder = dirname($pdfPath);
        if (!is_dir($folder)) {
            mkdir($folder);
        }

        if ($billingPdf->saveAs($pdfPath)) {
            return $pdfPath;
        } else {
            throw new \Exception($billingPdf->getError());
        }
    }

    private function getRealPdfPath(Billing $billing): string
    {
        return $this->billingStorage->getAdapter()->getPathPrefix() . $billing->getBillingPath();
    }

    public function getPdfPath(Billing $billing): ?string
    {
        return $this->billingStorage->has($billing->getBillingPath()) ? $this->getRealPdfPath($billing) : null;
    }
}