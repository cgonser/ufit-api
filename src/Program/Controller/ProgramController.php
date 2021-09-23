<?php

declare(strict_types=1);

namespace App\Program\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Program\Dto\ProgramDto;
use App\Program\Provider\ProgramProvider;
use App\Program\ResponseMapper\ProgramResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramController extends AbstractController
{
    public function __construct(
        private ProgramProvider $programProvider,
        private ProgramResponseMapper $programResponseMapper,
    ) {
    }

//    /**
//     * @OA\Tag(name="Program")
//     * @OA\Response(
//     *     response=200,
//     *     description="Returns the information about all programs",
//     *     @OA\JsonContent(
//     *         type="array",
//     *         @OA\Items(ref=@Model(type=ProgramDto::class)))
//     *     )*
//     * )
//     * @Security(name="Bearer")
//     */
//    #[Route(path: '/programs', name: 'programs_get', methods: 'GET')]
//    public function getPrograms(): ApiJsonResponse
//    {
//        $programs = $this->programProvider->findAll();
//
//        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->mapMultiple($programs));
//    }

//    /**
//     *
//     * @OA\Tag(name="Program")
//     * @OA\Response(
//     *     response=200,
//     *     description="Returns the information about a program",
//     *     @OA\JsonContent(ref=@Model(type=ProgramDto::class))
//     * )
//     */
//    #[Route(path: '/programs/{programId}', methods: 'GET', name: 'programs_get_one')]
//    public function getProgram(string $programId): ApiJsonResponse
//    {
//        $program = $this->programProvider->get(Uuid::fromString($programId));
//
//        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->map($program));
//    }
}
