<?php

declare(strict_types=1);

namespace App\Vendor\Controller\BankAccount;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorBankAccountDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorBankAccountRequest;
use App\Vendor\ResponseMapper\VendorBankAccountResponseMapper;
use App\Vendor\Service\VendorBankAccountRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/vendors/{vendorId}/bank_accounts')]
class VendorBankAccountCreateController extends AbstractController
{
    public function __construct(
        private VendorBankAccountResponseMapper $vendorBankAccountResponseMapper,
        private VendorBankAccountRequestManager $vendorBankAccountRequestManager,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorBankAccountRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=VendorBankAccountDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route(name: 'vendor_bank_accounts_create', methods: 'POST')]
    #[ParamConverter(data: 'vendor
    BankAccountRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function create(
        string $vendorId,
        VendorBankAccountRequest $vendorBankAccountRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): ApiJsonResponse {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }

        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorBankAccountRequest->vendorId = $vendor->getId()
            ->toString();
        $vendorBankAccount = $this->vendorBankAccountRequestManager->createFromRequest($vendorBankAccountRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->vendorBankAccountResponseMapper->map($vendorBankAccount)
        );
    }
}
