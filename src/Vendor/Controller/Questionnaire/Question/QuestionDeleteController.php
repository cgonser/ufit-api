<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Questionnaire\Question;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\QuestionProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\QuestionService;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/questionnaires/{questionnaireId}/questions')]
class QuestionDeleteController extends AbstractController
{
    public function __construct(
        private QuestionnaireProvider $questionnaireProvider,
        private QuestionProvider $questionProvider,
        private QuestionService $questionService,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(response=204, description="Deletes a question")
     * @OA\Response(response=404, description="Questionnaire not found | Question not found")
     */
    #[Route(path: '/{questionId}', name: 'questionnaires_questions_delete', methods: 'DELETE')]
    public function deleteQuestion(string $vendorId, string $questionnaireId, string $questionId): Response
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $questionnaire = $this->questionnaireProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($questionnaireId)
        );

        $question = $this->questionProvider->getByQuestionnaireAndId(
            $questionnaire,
            Uuid::fromString($questionId)
        );

        $this->questionService->delete($question);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
