<?php

namespace App\Vendor\Controller\Questionnaire\Question;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\QuestionDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Provider\QuestionnaireProvider;
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
use Symfony\Component\Validator\ConstraintViolationListInterface;

class QuestionCreateController extends AbstractController
{
    private QuestionnaireProvider $questionnaireProvider;

    private QuestionResponseMapper $questionResponseMapper;

    private QuestionService $questionService;

    public function __construct(
        QuestionnaireProvider $questionnaireProvider,
        QuestionResponseMapper $questionResponseMapper,
        QuestionService $questionService
    ) {
        $this->questionnaireProvider = $questionnaireProvider;
        $this->questionResponseMapper = $questionResponseMapper;
        $this->questionService = $questionService;
    }

    /**
     * @Route("/questionnaires/{questionnaireId}/questions", methods="POST", name="questionnaires_questions_create")
     *
     * @ParamConverter("questionRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=QuestionRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new questionnaire",
     *     @OA\JsonContent(ref=@Model(type=QuestionDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Questionnaire not found"
     * )
     */
    public function create(
        string $questionnaireId,
        QuestionRequest $questionRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            // TODO: implement authorization
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendor, Uuid::fromString($questionnaireId)
            );

            $question = $this->questionService->create($questionnaire, $questionRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->questionResponseMapper->map($question)
            );
        } catch (QuestionnaireNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
