<?php

namespace App\Controller\Front;

use App\Entity\Billing;
use League\Flysystem\FilesystemInterface;
use mikehaertl\wkhtmlto\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/billings", name="billing.")
 */
class BillingController extends AbstractController
{
    /**
     * @Route("/export/{billingNumber}", name="export", methods={"GET"})
     * @Entity("billing", expr="repository.getWithAllRelations(billingNumber)")
     * @IsGranted(App\Security\Voter\BillingVoter::EXPORT, subject="billing")
     */
    public function export (Billing $billing, Request $request, FilesystemInterface $billingStorage)
    {
        $completePath = $billingStorage->getAdapter()->getPathPrefix() . $billing->getBillingPath();

        if ($request->query->getInt('force') === 1 || !$billingStorage->has($billing->getBillingPath())) {
            $billingPdf = new Pdf();
            $billingPdf->addPage($this->renderView('billing/show.html.twig', ['billing' => $billing]));
            $billingPdf->saveAs($completePath);
        }

        return new BinaryFileResponse($completePath);
    }

    /**
     * @Route("/view/{billingNumber}", name="view", methods={"GET"})
     * @Entity("billing", expr="repository.getWithAllRelations(billingNumber)")
     * @IsGranted(App\Security\Voter\BillingVoter::VIEW, subject="billing")
     */
    public function view (Billing $billing)
    {
        $areAssetsAbsolute = false;
        $this->getUser();
        return $this->render('billing/show.html.twig', compact('billing', 'areAssetsAbsolute'));
    }
}
