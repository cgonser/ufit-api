<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonException;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use App\Vendor\Exception\VendorInstagramLoginFailedException;
use App\Vendor\Exception\VendorInstagramLoginMissingEmailException;
use App\Vendor\Request\VendorInstagramLoginRequest;
use App\Vendor\Service\VendorInstagramManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorInstagramController extends AbstractController
{
    public function __construct(
        private VendorInstagramManager $vendorInstagramManager,
        private AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
    }

    /**
     * @OA\Tag(name="Vendor")
     */
    #[Route(path: '/vendors/login/instagram', methods: 'POST', name: 'vendor_instagram_login')]
    #[ParamConverter(data: 'vendorInstagramLoginRequest', converter: 'fos_rest.request_body', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ])]
    public function login(VendorInstagramLoginRequest $vendorInstagramLoginRequest): Response
    {
        try {
            $vendor = $this->vendorInstagramManager->prepareVendorFromInstagramCode(
                $vendorInstagramLoginRequest->code,
                $vendorInstagramLoginRequest->email
            );

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($vendor);
        } catch (VendorInstagramLoginMissingEmailException | VendorEmailAddressInUseException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (VendorInstagramLoginFailedException $vendorInstagramLoginFailedException) {
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED, $vendorInstagramLoginFailedException->getMessage());
        }
    }
}
