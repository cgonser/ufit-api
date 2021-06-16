<?php

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerPhotoNotFoundException;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\ResponseMapper\CustomerPhotoResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerPhotoController extends AbstractController
{
    private CustomerPhotoResponseMapper $customerPhotoResponseMapper;

    private CustomerPhotoProvider $customerPhotoProvider;

    public function __construct(
        CustomerPhotoProvider $customerPhotoProvider,
        CustomerPhotoResponseMapper $customerPhotoResponseMapper
    ) {
        $this->customerPhotoResponseMapper = $customerPhotoResponseMapper;
        $this->customerPhotoProvider = $customerPhotoProvider;
    }

    /**
     * @Route("/customers/{customerId}/photos", methods="GET", name="customers_photos_get")
     *
     * @OA\Tag(name="Customer / Photo")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer photos",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=CustomerPhotoDto::class)))
     *     )
     * )
     *
     * @Security(name="Bearer")
     */
    public function getCustomerPhotos(string $customerId): Response
    {
        if ('current' == $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $customerPhotos = $this->customerPhotoProvider->findByCustomer($customer);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerPhotoResponseMapper->mapMultiple($customerPhotos));
    }

    /**
     * @Route("/customers/{customerId}/photos/{customerPhotoId}", methods="GET", name="customers_photos_get_one")
     *
     * @OA\Tag(name="Customer / Photo")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a photo",
     *     @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getCustomerPhoto(string $customerId, string $customerPhotoId): Response
    {
        try {
            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $customerPhoto = $this->customerPhotoProvider->getByCustomerAndId($customer, Uuid::fromString($customerPhotoId));

            return new ApiJsonResponse(Response::HTTP_OK, $this->customerPhotoResponseMapper->map($customerPhoto));
        } catch (CustomerPhotoNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
