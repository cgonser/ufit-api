<?php

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\ResponseMapper\QuestionnaireResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionnaireController extends AbstractController
{
    private QuestionnaireResponseMapper $questionnaireResponseMapper;

    private QuestionnaireProvider $questionnaireProvider;

    public function __construct(
        QuestionnaireResponseMapper $questionnaireResponseMapper,
        QuestionnaireProvider $questionnaireProvider
    ) {
        $this->questionnaireResponseMapper = $questionnaireResponseMapper;
        $this->questionnaireProvider = $questionnaireProvider;
    }

    /**
     * @Route("/questionnaires", methods="GET", name="questionnaires_get")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=200,
     *     description="Lists questionnaires",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=QuestionnaireDto::class)))
     *     )
     * )
     *
     * @Security(name="Bearer")
     */
    public function getQuestionnaires(): Response
    {
        // TODO: implement proper filtering
        /** @var Vendor $vendor */
        $vendor = $this->getUser();

        $questionnaires = $this->questionnaireProvider->findByVendor($vendor);

        return new ApiJsonResponse(Response::HTTP_OK, $this->questionnaireResponseMapper->mapMultiple($questionnaires));
    }
}
