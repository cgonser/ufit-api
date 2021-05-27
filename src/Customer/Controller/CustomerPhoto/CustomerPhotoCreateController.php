<?php

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerPhotoInvalidTakenAtException;
use App\Customer\Request\CustomerPhotoRequest;
use App\Customer\ResponseMapper\CustomerPhotoResponseMapper;
use App\Customer\Service\CustomerPhotoService;
use FOS\RestBundle\Controller\Annotations\FileParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerPhotoCreateController extends AbstractController
{
    private CustomerPhotoService $customerPhotoService;

    private CustomerPhotoResponseMapper $customerPhotoResponseMapper;

    public function __construct(
        CustomerPhotoService $customerPhotoService,
        CustomerPhotoResponseMapper $customerPhotoResponseMapper
    ) {
        $this->customerPhotoResponseMapper = $customerPhotoResponseMapper;
        $this->customerPhotoService = $customerPhotoService;
    }

    /**
     * @Route("/customers/{customerId}/photos", methods="POST", name="customers_photos_create")
     *
     * @ParamConverter("customerPhotoRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="CustomerPhoto")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerPhotoRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new photo",
     *     @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        string $customerId,
        CustomerPhotoRequest $customerPhotoRequest,
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

            $customerPhoto = $this->customerPhotoService->create(
                $customer,
                $customerPhotoRequest
            );

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->customerPhotoResponseMapper->map($customerPhoto)
            );
        } catch (CustomerPhotoInvalidTakenAtException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
