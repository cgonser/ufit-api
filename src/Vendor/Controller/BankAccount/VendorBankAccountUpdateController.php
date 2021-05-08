<?php

namespace App\Vendor\Controller\BankAccount;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorBankAccountDto;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorBankAccountRequest;
use App\Vendor\ResponseMapper\VendorBankAccountResponseMapper;
use App\Vendor\Service\VendorBankAccountRequestManager;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VendorBankAccountUpdateController extends AbstractController
{
    private VendorBankAccountRequestManager $vendorBankAccountManager;

    private VendorBankAccountProvider $vendorBankAccountProvider;

    private VendorBankAccountResponseMapper $vendorBankAccountResponseMapper;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorBankAccountRequestManager $vendorBankAccountManager,
        VendorBankAccountProvider $vendorBankAccountProvider,
        VendorBankAccountResponseMapper $vendorBankAccountResponseMapper,
        VendorProvider $vendorProvider
    ) {
        $this->vendorBankAccountManager = $vendorBankAccountManager;
        $this->vendorBankAccountProvider = $vendorBankAccountProvider;
        $this->vendorProvider = $vendorProvider;
        $this->vendorBankAccountResponseMapper = $vendorBankAccountResponseMapper;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/bank_accounts/{vendorBankAccountId}",
     *     methods="PUT",
     *     name="vendor_bank_accounts_update"
     * )
     * @ParamConverter("vendorBankAccountRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorBankAccountRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=VendorBankAccountDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function update(
        string $vendorId,
        string $vendorBankAccountId,
        VendorBankAccountRequest $vendorBankAccountRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorBankAccount = $this->vendorBankAccountProvider->getByVendorAndId(
            $vendor->getId(),
            Uuid::fromString($vendorBankAccountId)
        );

        $vendorBankAccountRequest->vendorId = $vendor->getId();
        $this->vendorBankAccountManager->updateFromRequest($vendorBankAccount, $vendorBankAccountRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorBankAccountResponseMapper->map($vendorBankAccount)
        );
    }
}
