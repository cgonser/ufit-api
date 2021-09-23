<?php

declare(strict_types=1);

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\QuestionnaireService;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/vendors/{vendorId}/questionnaires')]
class QuestionnaireDeleteController extends AbstractController
{
    public function __construct(
        private QuestionnaireService $questionnaireService,
        private QuestionnaireProvider $questionnaireProvider,
        private VendorProvider $vendorProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(response=204, description="Deletes a questionnaire")
     * @OA\Response(response=404, description="Questionnaire not found")
     */
    #[Route(path: '/{questionnaireId}', name: 'questionnaires_delete', methods: 'DELETE')]
    public function delete(string $vendorId, string $questionnaireId): Response
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($vendorId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $vendor);

        $questionnaire = $this->questionnaireProvider->getByVendorAndId(
            $vendor,
            Uuid::fromString($questionnaireId)
        );

        $this->questionnaireService->delete($questionnaire);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
