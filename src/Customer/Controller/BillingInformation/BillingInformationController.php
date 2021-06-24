<?php

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Dto\BillingInformationDto;
use App\Customer\Provider\BillingInformationProvider;
use App\Customer\Request\BillingInformationSearchRequest;
use App\Customer\ResponseMapper\BillingInformationResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BillingInformationController extends AbstractController
{
    private BillingInformationProvider $billingInformationProvider;

    private BillingInformationResponseMapper $billingInformationResponseMapper;

    public function __construct(
        BillingInformationProvider $billingInformationProvider,
        BillingInformationResponseMapper $billingInformationResponseMapper
    ) {
        $this->billingInformationProvider = $billingInformationProvider;
        $this->billingInformationResponseMapper = $billingInformationResponseMapper;
    }

    /**
     * @Route("/customers/{customerId}/billing_information", methods="GET", name="customer_billing_information_find")
     * @ParamConverter("searchRequest", converter="querystring")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=BillingInformationDto::class))))
     * )
     */
    public function getBillingInformationList(
        string $customerId,
        BillingInformationSearchRequest $searchRequest
    ): Response {
        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $searchRequest->customerId = $customer->getId();

        $results = $this->billingInformationProvider->search($searchRequest);
        $count = $this->billingInformationProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->billingInformationResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
