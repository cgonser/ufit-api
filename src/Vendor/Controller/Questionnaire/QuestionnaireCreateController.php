<?php

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Request\QuestionnaireRequest;
use App\Vendor\ResponseMapper\QuestionnaireResponseMapper;
use App\Vendor\Service\QuestionnaireService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class QuestionnaireCreateController extends AbstractController
{
    private QuestionnaireService $questionnaireService;

    private QuestionnaireResponseMapper $questionnaireResponseMapper;

    public function __construct(
        QuestionnaireService $questionnaireService,
        QuestionnaireResponseMapper $questionnaireResponseMapper
    ) {
        $this->questionnaireService = $questionnaireService;
        $this->questionnaireResponseMapper = $questionnaireResponseMapper;
    }

    /**
     * @Route("/questionnaires", methods="POST", name="questionnaires_create")
     *
     * @ParamConverter("questionnaireRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=QuestionnaireRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new questionnaire",
     *     @OA\JsonContent(ref=@Model(type=QuestionnaireDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        QuestionnaireRequest $questionnaireRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            if ('current' == $questionnaireRequest->vendorId) {
                /** @var Vendor $vendor */
                $vendor = $this->getUser();
            } else {
                // vendor fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $questionnaire = $this->questionnaireService->create($vendor, $questionnaireRequest);

            return new ApiJsonResponse(Response::HTTP_CREATED, $this->questionnaireResponseMapper->map($questionnaire));
        } catch (VendorPlanInvalidDurationException | CurrencyNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
