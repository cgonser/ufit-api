<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Dto\JWTAuthenticationTokenDto;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Vendor\Request\VendorRequest;
use App\Vendor\Service\VendorRequestManager;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class VendorCreateController extends AbstractController
{
    public function __construct(
        private VendorRequestManager $vendorRequestManager,
        private AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=JWTAuthenticationTokenDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/vendors', name: 'vendors_create', methods: ['POST'])]
    #[ParamConverter(data: 'vendorRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function create(VendorRequest $vendorRequest, Request $request,): JWTAuthenticationSuccessResponse
    {
//        if ($constraintViolationList->count() > 0) {
//            throw new ApiJsonInputValidationException($constraintViolationList);
//        }

        $vendor = $this->vendorRequestManager->createFromRequest($vendorRequest, $request->getClientIp());

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($vendor);
    }
}
