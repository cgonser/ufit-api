<?php

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Request\QuestionnaireUpdateRequest;
use App\Vendor\ResponseMapper\QuestionnaireResponseMapper;
use App\Vendor\Service\QuestionnaireService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class QuestionnaireUpdateController extends AbstractController
{
    private QuestionnaireService $questionnaireService;

    private QuestionnaireResponseMapper $questionnaireResponseMapper;

    private QuestionnaireProvider $questionnaireProvider;

    public function __construct(
        QuestionnaireService $questionnaireService,
        QuestionnaireProvider $questionnaireProvider,
        QuestionnaireResponseMapper $questionnaireResponseMapper
    ) {
        $this->questionnaireService = $questionnaireService;
        $this->questionnaireResponseMapper = $questionnaireResponseMapper;
        $this->questionnaireProvider = $questionnaireProvider;
    }

    /**
     * @Route("/questionnaires/{questionnaireId}", methods="PUT", name="questionnaires_update")
     *
     * @ParamConverter("questionnaireUpdateRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=QuestionnaireUpdateRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a questionnaire",
     *     @OA\JsonContent(ref=@Model(type=QuestionnaireDto::class))
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
    public function update(
        string $questionnaireId,
        QuestionnaireUpdateRequest $questionnaireUpdateRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            // TODO: implement proper authorization and token handling
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendor,
                Uuid::fromString($questionnaireId)
            );

            $this->questionnaireService->update($questionnaire, $questionnaireUpdateRequest);

            return new ApiJsonResponse(Response::HTTP_CREATED, $this->questionnaireResponseMapper->map($questionnaire));
        } catch (QuestionnaireNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
