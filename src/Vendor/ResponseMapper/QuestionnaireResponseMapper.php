<?php

declare(strict_types=1);

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Entity\Questionnaire;

class QuestionnaireResponseMapper
{
    public function __construct(
        private QuestionResponseMapper $questionResponseMapper
    ) {
    }

    public function map(Questionnaire $questionnaire): QuestionnaireDto
    {
        $questionnaireDto = new QuestionnaireDto();
        $questionnaireDto->id = $questionnaire->getId()
            ->toString();
        $questionnaireDto->vendorId = $questionnaire->getVendor()
            ->getId()
            ->toString();
        $questionnaireDto->title = $questionnaire->getTitle() ?? '';
        $questionnaireDto->questions = $this->questionResponseMapper->mapMultiple(
            $questionnaire->getQuestions()
                ->toArray()
        );

        return $questionnaireDto;
    }

    /**
     * @return QuestionnaireDto[]
     */
    public function mapMultiple(array $questionnaires): array
    {
        $questionnaireDtos = [];

        foreach ($questionnaires as $questionnaire) {
            $questionnaireDtos[] = $this->map($questionnaire);
        }

        return $questionnaireDtos;
    }
}
