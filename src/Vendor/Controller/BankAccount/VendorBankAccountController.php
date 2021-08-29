<?php

declare(strict_types=1);

namespace App\Vendor\Controller\BankAccount;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\VendorBankAccountDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorBankAccountSearchRequest;
use App\Vendor\ResponseMapper\VendorBankAccountResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/bank_accounts')]
class VendorBankAccountController extends AbstractController
{
    public function __construct(
        private VendorBankAccountProvider $vendorBankAccountProvider,
        private VendorBankAccountResponseMapper $vendorBankAccountResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @Security(name="Bearer")
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=VendorBankAccountDto::class))))
     * )
     */
    #[Route(name: 'vendor_bank_accounts_find', methods: 'GET')]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    public function getVendorBankAccounts(
        string $vendorId,
        VendorBankAccountSearchRequest $vendorBankAccountSearchRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $vendorBankAccountSearchRequest->vendorId = $vendor->getId()->toString();
        $vendorBankAccounts = $this->vendorBankAccountProvider->search($vendorBankAccountSearchRequest);
        $count = $this->vendorBankAccountProvider->count($vendorBankAccountSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorBankAccountResponseMapper->mapMultiple($vendorBankAccounts),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
