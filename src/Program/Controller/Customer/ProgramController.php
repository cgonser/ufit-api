<?php

declare(strict_types=1);

namespace App\Program\Controller\Customer;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Provider\CustomerProvider;
use App\Program\Dto\ProgramDto;
use App\Program\Provider\CustomerProgramProvider;
use App\Program\Request\CustomerProgramSearchRequest;
use App\Program\ResponseMapper\ProgramResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramController extends AbstractController
{
    public function __construct(
        private CustomerProgramProvider $customerProgramProvider,
        private ProgramResponseMapper $programResponseMapper,
        private CustomerProvider $customerProvider
    ) {
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=CustomerProgramSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=ProgramDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/customers/{customerId}/programs', name: 'customer_programs_find', methods: 'GET')]
    #[ParamConverter(data: 'customerProgramSearchRequest', converter: 'querystring')]
    public function getPrograms(
        string $customerId,
        CustomerProgramSearchRequest $customerProgramSearchRequest
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $programs = $this->customerProgramProvider->searchCustomerPrograms($customer, $customerProgramSearchRequest);
        $count = $this->customerProgramProvider->countCustomerPrograms($customer, $customerProgramSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programResponseMapper->mapMultiple($programs, true, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Program")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=ProgramDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/customers/{customerId}/programs/{programId}', name: 'customer_programs_get_one', methods: 'GET')]
    public function getProgram(string $customerId, string $programId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $program = $this->customerProgramProvider->getByCustomerAndId($customer, Uuid::fromString($programId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->map($program));
    }
}
