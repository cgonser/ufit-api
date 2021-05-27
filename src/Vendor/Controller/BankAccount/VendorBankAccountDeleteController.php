<?php

namespace App\Vendor\Controller\BankAccount;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorBankAccountManager;
use App\Vendor\Entity\Vendor;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorBankAccountDeleteController extends AbstractController
{
    private VendorBankAccountManager $vendorBankAccountManager;

    private VendorBankAccountProvider $vendorBankAccountProvider;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorBankAccountManager $vendorBankAccountManager,
        VendorBankAccountProvider $vendorBankAccountProvider,
        VendorProvider $vendorProvider
    ) {
        $this->vendorBankAccountManager = $vendorBankAccountManager;
        $this->vendorBankAccountProvider = $vendorBankAccountProvider;
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @Route(
     *     "/vendors/{vendorId}/bank_accounts/{vendorBankAccountId}",
     *     methods="DELETE",
     *     name="vendor_bank_accounts_delete"
     * )
     *
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function delete(string $vendorId, string $vendorBankAccountId): Response
    {
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

        $this->vendorBankAccountManager->delete($vendorBankAccount);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
