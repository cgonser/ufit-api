<?php

namespace App\Vendor\Controller;

use App\Vendor\Entity\VendorPlan;
use App\Vendor\Form\VendorPlanType;
use App\Vendor\Repository\VendorPlanRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan")
 * @IsGranted("ROLE_VENDOR")
 */
class VendorPlanController extends AbstractController
{
    /**
     * @Route("/", methods="GET", name="vendor_plan_index")
     */
    public function index(VendorPlanRepository $vendorPlanRepository): Response
    {
        $vendorPlans = $vendorPlanRepository->findBy(['vendor' => $this->getUser()]);

        return $this->render('vendor/plan/index.html.twig', [
            'vendor_plans' => $vendorPlans,
        ]);
    }

    /**
     * @Route("/new", methods="GET|POST", name="vendor_plan_new")
     */
    public function new(Request $request): Response
    {
        $vendorPlan = new VendorPlan();
        $vendorPlan->setVendor($this->getUser());

        $form = $this->createForm(VendorPlanType::class, $vendorPlan);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vendorPlan);
            $em->flush();

            $this->addFlash('success', 'vendor.plan.label.created_successfully');

            return $this->redirectToRoute('vendor_plan_index');
        }

        return $this->render('vendor/plan/new.html.twig', [
            'vendor_plan' => $vendorPlan,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id<\d+>}/edit", methods="GET|POST", name="vendor_plan_edit")
     * @IsGranted("edit", subject="vendorPlan", message="general.label.access_denied")
     */
    public function edit(Request $request, VendorPlan $vendorPlan): Response
    {
        $form = $this->createForm(VendorPlanType::class, $vendorPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'vendor.plan.label.updated_successfully');

            return $this->redirectToRoute('vendor_plan_edit', ['id' => $vendorPlan->getId()]);
        }

        return $this->render('vendor/plan/edit.html.twig', [
            'vendor_plan' => $vendorPlan,
            'form' => $form->createView(),
        ]);
    }
}