<?php

namespace App\Program\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
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
    private ProgramAssignmentProvider $programAssignmentProvider;

    private ProgramAssignmentResponseMapper $responseMapper;

    private CustomerProvider $customerProvider;

    public function __construct(
        ProgramAssignmentProvider $programAssignmentProvider,
        ProgramAssignmentResponseMapper $responseMapper,
        CustomerProvider $customerProvider
    ) {
        $this->programAssignmentProvider = $programAssignmentProvider;
        $this->customerProvider = $customerProvider;
        $this->responseMapper = $responseMapper;
    }

    /**
     * @Route("/customers/{customerId}/program_assignments", methods="GET", name="customer_program_assignments_find")
     * @ParamConverter("searchRequest", converter="querystring")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Program / Assignments")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=CustomerProgramSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=ProgramAssignmentDto::class))))
     * )
     */
    public function getProgramAssignments(string $customerId, ProgramAssignmentSearchRequest $searchRequest): Response
    {
        if ('current' == $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            if ($this->getUser() instanceof Customer) {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            // TODO: implement proper vendor authorization
            $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        }

        $searchRequest->customerId = $customer->getId();

        $assignments = $this->programAssignmentProvider->search($searchRequest);
        $count = $this->programAssignmentProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->responseMapper->mapMultiple($assignments, false, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
