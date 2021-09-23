<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Service\CustomerPhotoManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/customers/{customerId}/photos')]
class CustomerPhotoDeleteController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerPhotoManager $customerPhotoManager,
        private CustomerPhotoProvider $customerPhotoProvider
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Photo")
     * @OA\Response(response=204, description="Deletes a photo")
     * @OA\Response(response=404, description="Photo not found")
     */
    #[Route(path: '/{customerPhotoId}', name: 'customers_photos_delete', methods: 'DELETE')]
    public function delete(string $customerId, string $customerPhotoId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $customerPhoto = $this->customerPhotoProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerPhotoId)
        );

        $this->customerPhotoManager->delete($customerPhoto);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
