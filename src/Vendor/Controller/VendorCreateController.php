<?php

namespace App\Vendor\Controller;

use App\Core\Dto\JWTAuthenticationTokenDto;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Vendor\Request\VendorRequest;
use App\Vendor\Service\VendorRequestManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorCreateController extends AbstractController
{
    private VendorRequestManager $vendorRequestManager;

    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    public function __construct(
        VendorRequestManager $vendorRequestManager,
        AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
        $this->vendorRequestManager = $vendorRequestManager;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    /**
     * @Route("/vendors", methods="POST", name="vendors_create")
     * @ParamConverter("vendorRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=JWTAuthenticationTokenDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function create(
        VendorRequest $vendorRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $vendor = $this->vendorRequestManager->createFromRequest($vendorRequest);

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($vendor);
    }
}
