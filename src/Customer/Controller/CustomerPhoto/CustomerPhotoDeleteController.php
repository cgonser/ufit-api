<?php

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerPhotoInvalidDurationException;
use App\Customer\Exception\CustomerPhotoNotFoundException;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Request\CustomerPhotoUpdateRequest;
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

class CustomerPhotoDeleteController extends AbstractController
{
    private CustomerPhotoService $customerPhotoService;

    private CustomerPhotoProvider $customerPhotoProvider;

    public function __construct(
        CustomerPhotoService $customerPhotoService,
        CustomerPhotoProvider $customerPhotoProvider
    ) {
        $this->customerPhotoService = $customerPhotoService;
        $this->customerPhotoProvider = $customerPhotoProvider;
    }

    /**
     * @Route("/customers/{customerId}/photos/{customerPhotoId}", methods="DELETE", name="customers_photos_delete")
     *
     * @OA\Tag(name="CustomerPhoto")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a photo"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Plan not found"
     * )
     */
    public function create(
        string $customerId,
        string $customerPhotoId
    ): Response {
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerPhoto = $this->customerPhotoProvider->getByCustomerAndId($customer, Uuid::fromString($customerPhotoId));

            $this->customerPhotoService->delete($customerPhoto);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (CustomerPhotoNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
