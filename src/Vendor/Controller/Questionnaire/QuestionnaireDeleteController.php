<?php

namespace App\Vendor\Controller\Questionnaire;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Service\QuestionnaireService;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionnaireDeleteController extends AbstractController
{
    private QuestionnaireService $questionnaireService;

    private QuestionnaireProvider $questionnaireProvider;

    public function __construct(
        QuestionnaireService $questionnaireService,
        QuestionnaireProvider $questionnaireProvider
    ) {
        $this->questionnaireService = $questionnaireService;
        $this->questionnaireProvider = $questionnaireProvider;
    }

    /**
     * @Route("/questionnaires/{questionnaireId}", methods="DELETE", name="questionnaires_delete")
     *
     * @OA\Tag(name="Questionnaire")
     * @OA\Response(
     *     response=204,
     *     description="Deletes a questionnaire"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Questionnaire not found"
     * )
     */
    public function delete(string $questionnaireId): Response
    {
        try {
            // TODO: implement proper authorization and token handling
            /** @var Vendor $vendor */
            $vendor = $this->getUser();

            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendor,
                Uuid::fromString($questionnaireId)
            );

            $this->questionnaireService->delete($questionnaire);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (QuestionnaireNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
