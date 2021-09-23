<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerPhotoNotFoundException;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\ResponseMapper\CustomerPhotoResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/customers/{customerId}/photos')]
class CustomerPhotoController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerPhotoProvider $customerPhotoProvider,
        private CustomerPhotoResponseMapper $customerPhotoResponseMapper,
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Photo")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a customer photos",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CustomerPhotoDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(name: 'customers_photos_get', methods: 'GET')]
    public function getCustomerPhotos(string $customerId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $customerPhotos = $this->customerPhotoProvider->findByCustomer($customer);

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerPhotoResponseMapper->mapMultiple($customerPhotos));
    }

    /**
     * @OA\Tag(name="Customer / Photo")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/{customerPhotoId}', name: 'customers_photos_get_one', methods: 'GET')]
    public function getCustomerPhoto(string $customerId, string $customerPhotoId): Response
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $customerPhoto = $this->customerPhotoProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerPhotoId)
        );

        return new ApiJsonResponse(Response::HTTP_OK, $this->customerPhotoResponseMapper->map($customerPhoto));
    }
}
