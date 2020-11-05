<?php

namespace App\Vendor\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/login", name="vendor_signin")
     */
    public function login(Request $request, Security $security, AuthenticationUtils $helper): Response
    {
        if ($security->isGranted('ROLE_VENDOR')) {
            return $this->redirectToRoute('vendor_dashboard');
        }

        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('vendor_dashboard'));

        return $this->render('vendor/index.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="vendor_signout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
