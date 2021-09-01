<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Questionnaire\Question;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\QuestionDto;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\QuestionProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\QuestionRequest;
use App\Vendor\ResponseMapper\QuestionResponseMapper;
use App\Vendor\Service\QuestionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/questionnaires/{questionnaireId}/questions')]
class QuestionUpdateController extends AbstractController
{
    public function __construct(
        private QuestionnaireProvider $questionnaireProvider,
        private QuestionProvider $questionProvider,
        private QuestionService $questionService,
        private QuestionResponseMapper $questionResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=QuestionRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=QuestionDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Questionnaire not found | Question not found")
     */
    #[Route(path: '/{questionId}', name: 'questionnaires_questions_update', methods: 'PUT')]
    #[ParamConverter(data: 'questionRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function updateQuestion(
        string $vendorId,
        string $questionnaireId,
        string $questionId,
        QuestionRequest $questionRequest
    ): Response {
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

        $this->questionService->update($question, $questionRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->questionResponseMapper->map($question));
    }
}
