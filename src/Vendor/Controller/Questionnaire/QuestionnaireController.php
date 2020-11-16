<?php

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Request\QuestionnaireSearchRequest;
use App\Vendor\ResponseMapper\QuestionnaireResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @ParamConverter("questionnaireSearchRequest", converter="querystring")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\Parameter(
     *     in="query",
     *     name="filters",
     *     @OA\Schema(ref=@Model(type=QuestionnaireSearchRequest::class))
     * )
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
    public function getQuestionnaires(QuestionnaireSearchRequest $questionnaireSearchRequest): Response
    {
        if ('current' == $questionnaireSearchRequest->vendorId) {
            /** @var Vendor $vendor */
            $vendor = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $questionnaires = $this->questionnaireProvider->findByVendor($vendor);

        return new ApiJsonResponse(Response::HTTP_OK, $this->questionnaireResponseMapper->mapMultiple($questionnaires));
    }

    /**
     * @Route("/questionnaires/{questionnaireId}", methods="GET", name="questionnaires_get_one")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a questionnaire",
     *     @OA\JsonContent(ref=@Model(type=QuestionnaireDto::class))
     * )
     *
     * @Security(name="Bearer")
     */
    public function getQuestionnaire(string $questionnaireId): Response
    {
        try {
            // TODO: implement proper authorization and token handling
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendor,
                Uuid::fromString($questionnaireId)
            );

            return new ApiJsonResponse(Response::HTTP_OK, $this->questionnaireResponseMapper->map($questionnaire));
        } catch (QuestionnaireNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
