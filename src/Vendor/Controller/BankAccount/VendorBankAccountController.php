<?php

namespace App\Vendor\Controller\BankAccount;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\VendorBankAccountDto;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorBankAccountSearchRequest;
use App\Vendor\ResponseMapper\VendorBankAccountResponseMapper;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendorBankAccountController extends AbstractController
{
    private VendorBankAccountProvider $vendorBankAccountProvider;

    private VendorBankAccountResponseMapper $vendorBankAccountResponseMapper;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorBankAccountProvider $vendorBankAccountProvider,
        VendorBankAccountResponseMapper $vendorBankAccountResponseMapper,
        VendorProvider $vendorProvider
    ) {
        $this->vendorBankAccountProvider = $vendorBankAccountProvider;
        $this->vendorBankAccountResponseMapper = $vendorBankAccountResponseMapper;
        $this->vendorProvider = $vendorProvider;
    }

    /**
     * @Route("/vendors/{vendorId}/bank_accounts", methods="GET", name="vendor_bank_accounts_find")
     * @ParamConverter("searchRequest", converter="querystring")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Vendor / Bank Account")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=VendorBankAccountDto::class))))
     * )
     */
    public function getVendorBankAccounts(string $vendorId, VendorBankAccountSearchRequest $searchRequest): Response
    {
        if ('current' == $vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $searchRequest->vendorId = $vendor->getId();

        $vendorBankAccounts = $this->vendorBankAccountProvider->search($searchRequest);
        $count = $this->vendorBankAccountProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->vendorBankAccountResponseMapper->mapMultiple($vendorBankAccounts),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
