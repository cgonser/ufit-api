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
use App\Vendor\Request\QuestionUpdateRequest;
use App\Vendor\ResponseMapper\QuestionResponseMapper;
use App\Vendor\Service\QuestionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionUpdateController extends AbstractController
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
     * @Route("/questionnaires/{questionnaireId}/questions/{questionId}", methods="PUT", name="questionnaires_questions_update")
     *
     * @ParamConverter("questionUpdateRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=QuestionUpdateRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a question",
     *     @OA\JsonContent(ref=@Model(type=QuestionDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Questionnaire not found | Question not found"
     * )
     */
    public function updateQuestion(
        string $questionnaireId,
        string $questionId,
        QuestionUpdateRequest $questionUpdateRequest
    ): Response {
        try {
            // TODO: implement authorization
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendor, Uuid::fromString($questionnaireId)
            );

            $question = $this->questionProvider->getByQuestionnaireAndId(
                $questionnaire,
                Uuid::fromString($questionId)
            );

            $this->questionService->update($question, $questionUpdateRequest);

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->questionResponseMapper->map($question)
            );
        } catch (QuestionnaireNotFoundException | QuestionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
