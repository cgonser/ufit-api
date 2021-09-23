<?php

declare(strict_types=1);

namespace App\Vendor\Controller\BankAccount;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorBankAccountManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/bank_accounts')]
class VendorBankAccountDeleteController extends AbstractController
{
    public function __construct(
        private VendorBankAccountManager $vendorBankAccountManager,
        private VendorBankAccountProvider $vendorBankAccountProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route(path: '/{vendorBankAccountId}', name: 'vendor_bank_accounts_delete', methods: 'DELETE')]
    public function delete(string $vendorId, string $vendorBankAccountId): ApiJsonResponse
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorBankAccount = $this->vendorBankAccountProvider->getByVendorAndId(
            $vendor->getId(),
            Uuid::fromString($vendorBankAccountId)
        );
        $this->vendorBankAccountManager->delete($vendorBankAccount);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
