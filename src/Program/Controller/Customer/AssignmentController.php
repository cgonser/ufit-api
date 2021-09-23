<?php

declare(strict_types=1);

namespace App\Program\Controller\Customer;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Provider\CustomerProvider;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Provider\ProgramAssignmentProvider;
use App\Program\Request\CustomerProgramSearchRequest;
use App\Program\Request\ProgramAssignmentSearchRequest;
use App\Program\ResponseMapper\ProgramAssignmentResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssignmentController extends AbstractController
{
    public function __construct(
        private ProgramAssignmentProvider $programAssignmentProvider,
        private ProgramAssignmentResponseMapper $programAssignmentResponseMapper,
        private CustomerProvider $customerProvider
    ) {
    }

    /**
     * @OA\Tag(name="Program / Assignments")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=CustomerProgramSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=ProgramAssignmentDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/customers/{customerId}/program_assignments',
        name: 'customer_program_assignments_find',
        methods: 'GET'
    )]
    #[ParamConverter(data: 'programAssignmentSearchRequest', converter: 'querystring')]
    public function getProgramAssignments(
        string $customerId,
        ProgramAssignmentSearchRequest $programAssignmentSearchRequest
    ): ApiJsonResponse {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $programAssignmentSearchRequest->customerId = $customer->getId()->toString();
        $assignments = $this->programAssignmentProvider->search($programAssignmentSearchRequest);
        $count = $this->programAssignmentProvider->count($programAssignmentSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programAssignmentResponseMapper->mapMultiple($assignments, false, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
