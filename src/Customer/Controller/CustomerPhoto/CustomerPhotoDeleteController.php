<?php

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Service\CustomerPhotoManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerPhotoDeleteController extends AbstractController
{
    private CustomerPhotoManager $customerPhotoManager;

    private CustomerPhotoProvider $customerPhotoProvider;

    public function __construct(
        CustomerPhotoManager $customerPhotoManager,
        CustomerPhotoProvider $customerPhotoProvider
    ) {
        $this->customerPhotoManager = $customerPhotoManager;
        $this->customerPhotoProvider = $customerPhotoProvider;
    }

    /**
     * @Route("/customers/{customerId}/photos/{customerPhotoId}", methods="DELETE", name="customers_photos_delete")
     *
     * @OA\Tag(name="Customer / Photo")
     * @OA\Response(response=204, description="Deletes a photo")
     * @OA\Response(response=404, description="Photo not found")
     */
    public function delete(string $customerId, string $customerPhotoId): Response
    {
        if ('current' === $customerId) {
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

        $this->customerPhotoManager->delete($customerPhoto);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
