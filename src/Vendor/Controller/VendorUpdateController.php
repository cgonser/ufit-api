<?php

declare(strict_types=1);

namespace App\Vendor\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorRequest;
use App\Vendor\ResponseMapper\VendorResponseMapper;
use App\Vendor\Service\VendorRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class VendorUpdateController extends AbstractController
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorRequestManager $vendorRequestManager,
        private VendorResponseMapper $vendorResponseMapper,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorRequest::class)))
     * @OA\Response(response=200, description="Updates a vendor", @OA\JsonContent(ref=@Model(type=VendorDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/vendors/{vendorId}', name: 'vendors_update', methods: ['PATCH', 'PUT'])]
    #[ParamConverter(
        'vendorRequest',
        options: [
            'deserializationContext' => [
                'allow_extra_attributes' => false,
            ],
        ],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        string $vendorId,
        VendorRequest $vendorRequest,
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $this->vendorRequestManager->updateFromRequest($vendor, $vendorRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->vendorResponseMapper->map($vendor));
    }
}
