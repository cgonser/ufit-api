<?php

declare(strict_types=1);

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\BillingInformationDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\BillingInformationProvider;
use App\Customer\Provider\CustomerProvider;
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

#[Route(path: '/customers/{customerId}/billing_information')]
class BillingInformationController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private BillingInformationProvider $billingInformationProvider,
        private BillingInformationResponseMapper $billingInformationResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=BillingInformationDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(name: 'customer_billing_information_find', methods: 'GET')]
    #[ParamConverter(data: 'billingInformationSearchRequest', converter: 'querystring')]
    public function getBillingInformationList(
        string $customerId,
        BillingInformationSearchRequest $billingInformationSearchRequest
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $billingInformationSearchRequest->customerId = $customer->getId();

        $results = $this->billingInformationProvider->search($billingInformationSearchRequest);
        $count = $this->billingInformationProvider->count($billingInformationSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->billingInformationResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
