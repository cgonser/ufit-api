<?php

namespace App\Vendor\Controller;

use App\Vendor\Form\VendorChangePasswordType;
use App\Vendor\Form\VendorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_VENDOR")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", methods="GET|POST", name="vendor_profile_edit")
     */
    public function edit(Request $request): Response
    {
        $vendor = $this->getUser();

        $form = $this->createForm(VendorType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'vendor_profile.updated_successfully');

            return $this->redirectToRoute('vendor_profile_edit');
        }

        return $this->render('vendor/profile/edit.html.twig', [
            'vendor' => $vendor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-password", methods="GET|POST", name="vendor_change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $vendor = $this->getUser();

        $form = $this->createForm(VendorChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vendor->setPassword($encoder->encodePassword($vendor, $form->get('newPassword')->getData()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vendor_signout');
        }

        return $this->render('vendor/profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
