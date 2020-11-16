<?php

namespace App\Vendor\Controller\Questionnaire\Question;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\QuestionDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Exception\QuestionNotFoundException;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\QuestionProvider;
use App\Vendor\ResponseMapper\QuestionResponseMapper;
use App\Vendor\Service\QuestionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private QuestionnaireProvider $questionnaireProvider;

    private QuestionProvider $questionProvider;

    private QuestionService $questionService;

    private QuestionResponseMapper $questionResponseMapper;

    public function __construct(
        QuestionnaireProvider $questionnaireProvider,
        QuestionProvider $questionProvider,
        QuestionService $questionService,
        QuestionResponseMapper $questionResponseMapper
    ) {
        $this->questionnaireProvider = $questionnaireProvider;
        $this->questionProvider = $questionProvider;
        $this->questionService = $questionService;
        $this->questionResponseMapper = $questionResponseMapper;
    }

    /**
     * @Route("/questionnaires/{questionnaireId}/questions", methods="GET", name="questionnaires_questions_get")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=200,
     *     description="Returns all questions of a given questionnaire",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=QuestionDto::class)))
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Questionnaire not found"
     * )
     */
    public function getQuestions(string $questionnaireId): Response
    {
        try {
            // TODO: implement authorization
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendor, Uuid::fromString($questionnaireId)
            );

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->questionResponseMapper->mapMultiple($questionnaire->getQuestions()->toArray())
            );
        } catch (QuestionnaireNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }

    /**
     * @Route(
     *     "/questionnaires/{questionnaireId}/questions/{questionId}",
     *     methods="GET",
     *     name="questionnaires_questions_get_one"
     * )
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a question",
     *     @OA\JsonContent(ref=@Model(type=QuestionDto::class))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Questionnaire not found | Question not found"
     * )
     */
    public function getQuestion(string $questionnaireId, string $questionId): Response
    {
        try {
            // TODO: implement proper authorization and token handling
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendor,
                Uuid::fromString($questionnaireId)
            );

            $question = $this->questionProvider->getByQuestionnaireAndId(
                $questionnaire,
                Uuid::fromString($questionId)
            );

            return new ApiJsonResponse(Response::HTTP_OK, $this->questionResponseMapper->map($question));
        } catch (QuestionnaireNotFoundException | QuestionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
