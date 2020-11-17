<?php

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerPhotoInvalidTakenAtException;
use App\Customer\Exception\CustomerPhotoNotFoundException;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Request\CustomerPhotoRequest;
use App\Customer\ResponseMapper\CustomerPhotoResponseMapper;
use App\Customer\Service\CustomerPhotoService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerPhotoUpdateController extends AbstractController
{
    private CustomerPhotoProvider $customerPhotoProvider;

    private CustomerPhotoService $customerPhotoService;

    private CustomerPhotoResponseMapper $customerPhotoResponseMapper;

    public function __construct(
        CustomerPhotoProvider $customerPhotoProvider,
        CustomerPhotoService $customerPhotoService,
        CustomerPhotoResponseMapper $customerPhotoResponseMapper
    ) {
        $this->customerPhotoResponseMapper = $customerPhotoResponseMapper;
        $this->customerPhotoProvider = $customerPhotoProvider;
        $this->customerPhotoService = $customerPhotoService;
    }

    /**
     * @Route("/customers/{customerId}/photos/{customerPhotoId}", methods="PUT", name="customers_photos_update")
     *
     * @ParamConverter("customerPhotoRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="CustomerPhoto")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerPhotoRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a photo",
     *     @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Plan not found"
     * )
     */
    public function create(
        string $customerId,
        string $customerPhotoId,
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

            $customerPhoto = $this->customerPhotoProvider->getByCustomerAndId(
                $customer,
                Uuid::fromString($customerPhotoId)
            );

            $this->customerPhotoService->update($customerPhoto, $customerPhotoRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->customerPhotoResponseMapper->map($customerPhoto)
            );
        } catch (CustomerPhotoNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (CustomerPhotoInvalidTakenAtException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
