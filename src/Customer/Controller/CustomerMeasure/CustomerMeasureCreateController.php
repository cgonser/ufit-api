<?php

namespace App\Customer\Controller\CustomerMeasure;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerMeasureDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerMeasureInvalidTakenAtException;
use App\Customer\Request\CustomerMeasureRequest;
use App\Customer\ResponseMapper\CustomerMeasureResponseMapper;
use App\Customer\Service\CustomerMeasureService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerMeasureCreateController extends AbstractController
{
    private CustomerMeasureService $customerMeasureService;

    private CustomerMeasureResponseMapper $customerMeasureResponseMapper;

    public function __construct(
        CustomerMeasureService $customerMeasureService,
        CustomerMeasureResponseMapper $customerMeasureResponseMapper
    ) {
        $this->customerMeasureResponseMapper = $customerMeasureResponseMapper;
        $this->customerMeasureService = $customerMeasureService;
    }

    /**
     * @Route("/customers/{customerId}/measures", methods="POST", name="customers_measures_create")
     *
     * @ParamConverter("customerMeasureRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="CustomerMeasure")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasureRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new measure",
     *     @OA\JsonContent(ref=@Model(type=CustomerMeasureDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        string $customerId,
        CustomerMeasureRequest $customerMeasureRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerMeasure = $this->customerMeasureService->create($customer, $customerMeasureRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->customerMeasureResponseMapper->map($customerMeasure)
            );
        } catch (CustomerMeasureInvalidTakenAtException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
