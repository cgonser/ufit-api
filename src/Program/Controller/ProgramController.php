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
    private ProgramProvider $programProvider;

    private ProgramResponseMapper $programResponseMapper;

    public function __construct(ProgramProvider $programProvider, ProgramResponseMapper $programResponseMapper)
    {
        $this->programProvider = $programProvider;
        $this->programResponseMapper = $programResponseMapper;
    }

    /**
     * @Route("/programs", methods="GET", name="programs_get")
     *
     * @OA\Tag(name="Program")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about all programs",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=ProgramDto::class)))
     *     )*
     * )
     *
     * @Security(name="Bearer")
     */
    public function getPrograms(): Response
    {
        $programs = $this->programProvider->findAll();

        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->mapMultiple($programs));
    }

    /**
     * @Route("/programs/{programId}", methods="GET", name="programs_get_one")
     *
     * @OA\Tag(name="Program")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a program",
     *     @OA\JsonContent(ref=@Model(type=ProgramDto::class))
     * )
     */
    public function getProgram(string $programId): Response
    {
        $program = $this->programProvider->get(Uuid::fromString($programId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->programResponseMapper->map($program));
    }
}
