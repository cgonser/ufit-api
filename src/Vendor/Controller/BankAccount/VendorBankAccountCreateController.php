<?php

namespace App\Vendor\Controller\BankAccount;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorBankAccountDto;
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

class VendorBankAccountCreateController extends AbstractController
{
    private VendorBankAccountResponseMapper $vendorBankAccountResponseMapper;

    private VendorBankAccountRequestManager $vendorBankAccountManager;

    public function __construct(
        VendorBankAccountResponseMapper $vendorBankAccountResponseMapper,
        VendorBankAccountRequestManager $vendorBankAccountManager
    ) {
        $this->vendorBankAccountResponseMapper = $vendorBankAccountResponseMapper;
        $this->vendorBankAccountManager = $vendorBankAccountManager;
    }

    /**
     * @Route("/vendors/{vendorId}/bank_accounts", methods="POST", name="vendor_bank_accounts_create")
     * @ParamConverter("vendorBankAccountRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=VendorBankAccountRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=VendorBankAccountDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function create(
        string $vendorId,
        VendorBankAccountRequest $vendorBankAccountRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $vendorBankAccountRequest->vendorId = $vendor->getId();
        $vendorBankAccount = $this->vendorBankAccountManager->createFromRequest($vendorBankAccountRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->vendorBankAccountResponseMapper->map($vendorBankAccount)
        );
    }
}
