<?php

namespace App\Customer\Controller\CustomerMeasure;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasureDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasureNotFoundException;
use App\Customer\Provider\CustomerMeasureProvider;
use App\Customer\ResponseMapper\CustomerMeasureResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerMeasureController extends AbstractController
{
    private CustomerMeasureResponseMapper $customerMeasureResponseMapper;

    private CustomerMeasureProvider $customerMeasureProvider;

    public function __construct(
        CustomerMeasureProvider $customerMeasureProvider,
        CustomerMeasureResponseMapper $customerMeasureResponseMapper
    ) {
        $this->customerMeasureResponseMapper = $customerMeasureResponseMapper;
        $this->customerMeasureProvider = $customerMeasureProvider;
    }

    /**
     * @Route("/customers/{customerId}/measures", methods="GET", name="customers_measures_get")
     *
     * @OA\Tag(name="CustomerMeasure")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer measures",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=CustomerMeasureDto::class)))
     *     )
     * )
     *
     * @Security(name="Bearer")
     */
    public function getCustomerMeasures(string $customerId): Response
    {
        if ('current' == $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $customerMeasures = $this->customerMeasureProvider->findByCustomer($customer);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerMeasureResponseMapper->mapMultiple($customerMeasures));
    }

    /**
     * @Route("/customers/{customerId}/measures/{customerMeasureId}", methods="GET", name="customers_measures_get_one")
     *
     * @OA\Tag(name="CustomerMeasure")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a measure",
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasureDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getCustomerMeasure(string $customerId, string $customerMeasureId): Response
    {
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerMeasure = $this->customerMeasureProvider->getByCustomerAndId($customer, Uuid::fromString($customerMeasureId));

            return new ApiJsonResponse(Response::HTTP_OK, $this->customerMeasureResponseMapper->map($customerMeasure));
        } catch (CustomerMeasureNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
