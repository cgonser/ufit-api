<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerPhotoProvider;
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

class CustomerPhotoUpdateController extends AbstractController
{
    private CustomerPhotoProvider $customerPhotoProvider;

    private CustomerPhotoRequestManager $customerPhotoRequestManager;

    private CustomerPhotoResponseMapper $customerPhotoResponseMapper;

    public function __construct(
        CustomerPhotoProvider $customerPhotoProvider,
        CustomerPhotoRequestManager $customerPhotoRequestManager,
        CustomerPhotoResponseMapper $customerPhotoResponseMapper
    ) {
        $this->customerPhotoProvider = $customerPhotoProvider;
        $this->customerPhotoRequestManager = $customerPhotoRequestManager;
        $this->customerPhotoResponseMapper = $customerPhotoResponseMapper;
    }

    /**
     * @Route("/customers/{customerId}/photos/{customerPhotoId}", methods="PUT", name="customers_photos_update")
     * @ParamConverter("customerPhotoRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Customer / Photo")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerPhotoRequest::class)))
     * @OA\Response(response=200, description="Uploads a photo", @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Photo not found")
     */
    public function update(
        string $customerId,
        string $customerPhotoId,
        CustomerPhotoRequest $customerPhotoRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

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

        $this->customerPhotoRequestManager->update($customerPhoto, $customerPhotoRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->customerPhotoResponseMapper->map($customerPhoto)
        );
    }

    /**
     * @Route("/customers/{customerId}/photos/{customerPhotoId}/file", methods="PUT", name="customers_photos_upload")
     *
     * @OA\Tag(name="Customer / Photo")
     * @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/octet-stream", @OA\Schema(type="string", format="binary")))
     * @OA\Response(response=200, description="Uploads a photo", @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Photo not found")
     */
    public function upload(string $customerId, string $customerPhotoId, Request $request): Response
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

        $this->customerPhotoRequestManager->uploadPhoto($customerPhoto, $request->getContent());

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->customerPhotoResponseMapper->map($customerPhoto)
        );
    }
}
