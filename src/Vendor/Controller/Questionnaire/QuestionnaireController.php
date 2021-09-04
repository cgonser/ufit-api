<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\VendorProvider;
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

#[Route(path: '/vendors/{vendorId}/questionnaires')]
class QuestionnaireController extends AbstractController
{
    public function __construct(
        private QuestionnaireResponseMapper $questionnaireResponseMapper,
        private QuestionnaireProvider $questionnaireProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=QuestionnaireSearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Lists questionnaires",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=QuestionnaireDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(name: 'questionnaires_get', methods: 'GET')]
    #[ParamConverter(data: 'questionnaireSearchRequest', converter: 'querystring')]
    public function getQuestionnaires(
        string $vendorId,
        QuestionnaireSearchRequest $questionnaireSearchRequest
    ): ApiJsonResponse {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $questionnaireSearchRequest->vendorId = $vendor->getId()
            ->toString();
        $questionnaires = $this->questionnaireProvider->search($questionnaireSearchRequest);
        $count = $this->questionnaireProvider->count($questionnaireSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->questionnaireResponseMapper->mapMultiple($questionnaires),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=200,
     *     description="Returns the information about a questionnaire",
     *     @OA\JsonContent(ref=@Model(type=QuestionnaireDto::class))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/{questionnaireId}', name: 'questionnaires_get_one', methods: 'GET')]
    public function getQuestionnaire(string $vendorId, string $questionnaireId): Response
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $vendor);

        $questionnaire = $this->questionnaireProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($questionnaireId)
        );

        return new ApiJsonResponse(Response::HTTP_OK, $this->questionnaireResponseMapper->map($questionnaire));
    }
}
