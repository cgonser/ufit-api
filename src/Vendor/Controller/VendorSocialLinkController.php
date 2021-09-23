<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorSocialLinkRequest;
use App\Vendor\Service\VendorRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorSocialLinkController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorRequestManager $vendorRequestManager
    ) {
    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorSocialLinkRequest::class)))
     * @OA\Response(response=204, description="Defines a social network link")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/vendors/{vendorId}/socialLinks', methods: 'PUT', name: 'vendors_social_links_put')]
    #[ParamConverter(data: 'vendorSocialLinkRequest', converter: 'fos_rest.request_body', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ])]
    public function create(
        string $vendorId,
        VendorSocialLinkRequest $vendorSocialLinkRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);
        $this->vendorRequestManager->updateSocialLink($vendor, $vendorSocialLinkRequest);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
