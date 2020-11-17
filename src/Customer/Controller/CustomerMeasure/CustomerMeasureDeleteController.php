<?php

namespace App\Customer\Controller\CustomerMeasure;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasureDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasureInvalidDurationException;
use App\Customer\Exception\CustomerMeasureNotFoundException;
use App\Customer\Provider\CustomerMeasureProvider;
use App\Customer\Request\CustomerMeasureUpdateRequest;
use App\Customer\ResponseMapper\CustomerMeasureResponseMapper;
use App\Customer\Service\CustomerMeasureService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerMeasureDeleteController extends AbstractController
{
    private CustomerMeasureService $customerMeasureService;

    private CustomerMeasureProvider $customerMeasureProvider;

    public function __construct(
        CustomerMeasureService $customerMeasureService,
        CustomerMeasureProvider $customerMeasureProvider
    ) {
        $this->customerMeasureService = $customerMeasureService;
        $this->customerMeasureProvider = $customerMeasureProvider;
    }

    /**
     * @Route("/customers/{customerId}/measures/{customerMeasureId}", methods="DELETE", name="customers_measures_delete")
     *
     * @OA\Tag(name="CustomerMeasure")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a measure"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Plan not found"
     * )
     */
    public function create(
        string $customerId,
        string $customerMeasureId
    ): Response {
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerMeasure = $this->customerMeasureProvider->getByCustomerAndId($customer, Uuid::fromString($customerMeasureId));

            $this->customerMeasureService->delete($customerMeasure);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (CustomerMeasureNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
