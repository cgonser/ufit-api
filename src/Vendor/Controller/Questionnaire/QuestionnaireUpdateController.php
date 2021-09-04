<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\QuestionnaireRequest;
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

#[Route(path: '/vendors/{vendorId}/questionnaires')]
class QuestionnaireUpdateController extends AbstractController
{
    public function __construct(
        private QuestionnaireService $questionnaireService,
        private QuestionnaireProvider $questionnaireProvider,
        private QuestionnaireResponseMapper $questionnaireResponseMapper,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=QuestionnaireRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=QuestionnaireDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=404, description="Questionnaire not found")
     */
    #[Route(path: '/{questionnaireId}', name: 'questionnaires_update', methods: 'PUT')]
    #[ParamConverter(data: 'questionnaireRequest', options: [
        'deserializationContext' => [
            'allow_extra_attributes' => false,
        ],
    ], converter: 'fos_rest.request_body')]
    public function update(
        string $vendorId,
        string $questionnaireId,
        QuestionnaireRequest $questionnaireRequest,
        ConstraintViolationListInterface $constraintViolationList
    ): Response {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }

        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $questionnaire = $this->questionnaireProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($questionnaireId)
        );

        $this->questionnaireService->update($questionnaire, $questionnaireRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->questionnaireResponseMapper->map($questionnaire));
    }
}
