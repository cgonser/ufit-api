<?php

declare(strict_types=1);

namespace App\Customer\Controller\CustomerPhoto;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Dto\CustomerPhotoDto;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Request\CustomerPhotoRequest;
use App\Customer\ResponseMapper\CustomerPhotoResponseMapper;
use App\Customer\Service\CustomerPhotoRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route(path: '/customers/{customerId}/photos')]
class CustomerPhotoCreateController extends AbstractController
{
    public function __construct(
        private CustomerProvider $customerProvider,
        private CustomerPhotoRequestManager $customerPhotoRequestManager,
        private CustomerPhotoResponseMapper $customerPhotoResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Customer / Photo")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CustomerPhotoRequest::class)))
     * @OA\Response(response=201, description="Creates a new photo", @OA\JsonContent(ref=@Model(type=CustomerPhotoDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @Security(name="Bearer")
     */
    #[Route(name: 'customers_photos_create', methods: 'POST')]
    #[ParamConverter(data: 'customerPhotoRequest', options: [
        'deserializationContext' => ['allow_extra_attributes' => false],
    ], converter: 'fos_rest.request_body')]
    public function create(
        string $customerId,
        CustomerPhotoRequest $customerPhotoRequest,
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $customer);

        $customerPhoto = $this->customerPhotoRequestManager->create($customer, $customerPhotoRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->customerPhotoResponseMapper->map($customerPhoto));
    }
}
