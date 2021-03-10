<?php

namespace App\Program\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Provider\CustomerProvider;
use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Program\Dto\ProgramDto;
use App\Program\Request\CustomerProgramSearchRequest;
use App\Program\ResponseMapper\ProgramResponseMapper;
use App\Customer\Entity\Customer;
use App\Program\Provider\CustomerProgramProvider;
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
    private CustomerProgramProvider $programProvider;

    private ProgramResponseMapper $programResponseMapper;

    private CustomerResponseMapper $customerResponseMapper;

    private CustomerProvider $customerProvider;

    public function __construct(
        CustomerProgramProvider $programProvider,
        ProgramResponseMapper $programResponseMapper,
        CustomerResponseMapper $customerResponseMapper,
        CustomerProvider $customerProvider
    ) {
        $this->programProvider = $programProvider;
        $this->programResponseMapper = $programResponseMapper;
        $this->customerResponseMapper = $customerResponseMapper;
        $this->customerProvider = $customerProvider;
    }

    /**
     * @Route("/customers/{customerId}/programs", methods="GET", name="customer_programs_find")
     * @ParamConverter("searchRequest", converter="querystring")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Program")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=CustomerProgramSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=ProgramDto::class))))
     * )
     */
    public function getPrograms(string $customerId, CustomerProgramSearchRequest $searchRequest): Response
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

        $programs = $this->programProvider->searchCustomerPrograms($customer, $searchRequest);
        $count = $this->programProvider->countCustomerPrograms($customer, $searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programResponseMapper->mapMultiple($programs, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @Route("/customers/{customerId}/programs/{programId}", methods="GET", name="customer_programs_get_one")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Program")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=ProgramDto::class)))
     */
    public function getProgram(string $customerId, string $programId): Response
    {
        if ('current' == $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $program = $this->programProvider->getByCustomerAndId($customer, Uuid::fromString($programId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->programResponseMapper->map($program)
        );
    }
}
