<?php

declare(strict_types=1);

namespace App\Vendor\Controller\BankAccount;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorBankAccountDto;
use App\Vendor\Provider\VendorBankAccountProvider;
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

#[Route(path: '/vendors/{vendorId}/bank_accounts')]
class VendorBankAccountUpdateController extends AbstractController
{
    public function __construct(
        private VendorBankAccountRequestManager $vendorBankAccountRequestManager,
        private VendorBankAccountProvider $vendorBankAccountProvider,
        private VendorBankAccountResponseMapper $vendorBankAccountResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorBankAccountRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=VendorBankAccountDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route(path: '/{vendorBankAccountId}', name: 'vendor_bank_accounts_update', methods: 'PUT')]
    #[ParamConverter(
        data: 'vendorBankAccountRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        string $vendorId,
        string $vendorBankAccountId,
        VendorBankAccountRequest $vendorBankAccountRequest,
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorBankAccount = $this->vendorBankAccountProvider->getByVendorAndId(
            $vendor->getId(),
            Uuid::fromString($vendorBankAccountId)
        );

        $vendorBankAccountRequest->vendorId = $vendor->getId()->toString();
        $this->vendorBankAccountRequestManager->updateFromRequest($vendorBankAccount, $vendorBankAccountRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorBankAccountResponseMapper->map($vendorBankAccount)
        );
    }
}
