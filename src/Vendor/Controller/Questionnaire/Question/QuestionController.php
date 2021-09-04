<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Questionnaire\Question;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\QuestionDto;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\QuestionProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\ResponseMapper\QuestionResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/questionnaires/{questionnaireId}/questions')]
class QuestionController extends AbstractController
{
    public function __construct(
        private QuestionnaireProvider $questionnaireProvider,
        private QuestionProvider $questionProvider,
        private QuestionResponseMapper $questionResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=QuestionDto::class))))
     * )
     * @OA\Response(response=404, description="Questionnaire not found")
     */
    #[Route(name: 'questionnaires_questions_get', methods: 'GET')]
    public function getQuestions(string $vendorId, string $questionnaireId): Response
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $questionnaire = $this->questionnaireProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($questionnaireId)
        );

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->questionResponseMapper->mapMultiple($questionnaire->getQuestions()->toArray())
        );
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=QuestionDto::class)))
     * @OA\Response(response=404, description="Questionnaire not found | Question not found")
     */
    #[Route(path: '/{questionId}', name: 'questionnaires_questions_get_one', methods: 'GET')]
    public function getQuestion(string $vendorId, string $questionnaireId, string $questionId): Response {
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

        return new ApiJsonResponse(Response::HTTP_OK, $this->questionResponseMapper->map($question));
    }
}
