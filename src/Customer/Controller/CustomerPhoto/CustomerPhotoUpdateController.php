<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerPhotoRequest;
use App\Customer\ResponseMapper\CustomerPhotoResponseMapper;
use App\Customer\Service\CustomerPhotoRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/customers/{customerId}/photos')]
class CustomerPhotoUpdateController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerPhotoProvider $customerPhotoProvider,
        private CustomerPhotoRequestManager $customerPhotoRequestManager,
        private CustomerPhotoResponseMapper $customerPhotoResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Photo")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerPhotoRequest::class)))
     * @OA\Response(response=200, description="Uploads a photo", @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Photo not found")
     */
    #[Route(path: '/{customerPhotoId}', name: 'customers_photos_update', methods: 'PUT')]
    #[ParamConverter(data: 'customerPhotoRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,

        ],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $customerId,
        string $customerPhotoId,
        CustomerPhotoRequest $customerPhotoRequest,
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $customerPhoto = $this->customerPhotoProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerPhotoId)
        );
        $this->customerPhotoRequestManager->update($customerPhoto, $customerPhotoRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->customerPhotoResponseMapper->map($customerPhoto));
    }

    /**
     * @OA\Tag(name="Customer / Photo")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=200, description="Uploads a photo", @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Photo not found")
     */
    #[Route(path: '/{customerPhotoId}/file', name: 'customers_photos_upload', methods: 'PUT')]
    public function upload(
        string $customerId,
        string $customerPhotoId,
        Request $request
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $customerPhoto = $this->customerPhotoProvider->getByCustomerAndId(
            $customer,
            Uuid::fromString($customerPhotoId)
        );
        $this->customerPhotoRequestManager->uploadPhoto($customerPhoto, $request->getContent());

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->customerPhotoResponseMapper->map($customerPhoto));
    }
}
