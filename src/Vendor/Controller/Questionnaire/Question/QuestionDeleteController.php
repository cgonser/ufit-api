<?php

namespace App\Vendor\Controller\Questionnaire\Question;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Exception\QuestionNotFoundException;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\QuestionProvider;
use App\Vendor\Service\QuestionService;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionDeleteController extends AbstractController
{
    private QuestionnaireProvider $questionnaireProvider;

    private QuestionProvider $questionProvider;

    private QuestionService $questionService;

    public function __construct(
        QuestionnaireProvider $questionnaireProvider,
        QuestionProvider $questionProvider,
        QuestionService $questionService
    ) {
        $this->questionnaireProvider = $questionnaireProvider;
        $this->questionProvider = $questionProvider;
        $this->questionService = $questionService;
    }

    /**
     * @Route("/questionnaires/{questionnaireId}/questions/{questionId}", methods="DELETE", name="questionnaires_questions_delete")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a question"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Questionnaire not found | Question not found"
     * )
     */
    public function deleteQuestion(
        string $questionnaireId,
        string $questionId
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

            $this->questionService->delete($question);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (QuestionnaireNotFoundException | QuestionNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
