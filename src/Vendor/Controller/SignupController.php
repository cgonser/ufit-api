<?php

namespace App\Vendor\Controller;

use App\Vendor\Entity\Vendor;
use App\Vendor\Form\VendorSignupType;
use App\Vendor\Service\VendorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends AbstractController
{
    private VendorManager $vendorManager;

    public function __construct(VendorManager $vendorManager)
    {
        $this->vendorManager = $vendorManager;
    }

    /**
     * @Route("/signup", name="vendor_signup")
     */
    public function signup(Request $request)
    {
        $vendor = new Vendor();
        $form = $this->createForm(VendorSignupType::class, $vendor);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->vendorManager->createVendor($vendor);

            return $this->redirectToRoute('vendor_dashboard');
        }

        return $this->render('vendor/signup/signup.html.twig', [
            'signup_form' => $form->createView(),
        ]);
    }

    public function form()
    {
        $form = $this->createForm(VendorSignupType::class);

        return $this->render('vendor/signup/form.html.twig', [
            'signup_form' => $form->createView(),
        ]);
    }
}
