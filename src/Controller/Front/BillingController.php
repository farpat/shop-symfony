<?php

namespace App\Controller\Front;

use App\Entity\Billing;
use App\Services\Shop\Bank\BillingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/billings", name="app_front_billing_")
 */
class BillingController extends AbstractController
{
    /**
     * @Route("/export/{billingNumber}", name="export", methods={"GET"})
     * @Entity("billing", expr="repository.getWithAllRelations(billingNumber)")
     * @IsGranted(App\Security\Voter\BillingVoter::EXPORT, subject="billing")
     */
    public function export(Billing $billing, Request $request, BillingService $billingService)
    {
        $pdfPath = $billingService->getPdfPath($billing);

        if ($request->query->getInt('force') === 1 || $pdfPath === null) {
            $pdfPath = $billingService->generatePdf($billing);
        }

        return new BinaryFileResponse($pdfPath);
    }

    /**
     * @Route("/view/{billingNumber}", name="view", methods={"GET"})
     * @Entity("billing", expr="repository.getWithAllRelations(billingNumber)")
     * @IsGranted(App\Security\Voter\BillingVoter::VIEW, subject="billing")
     */
    public function view(Billing $billing, BillingService $billingService, Request $request)
    {
        $areAssetsAbsolute = false;

        return $this->render('billing/show.html.twig', [
            'billing'           => $billing,
            'areAssetsAbsolute' => $areAssetsAbsolute
        ]);
    }
}
