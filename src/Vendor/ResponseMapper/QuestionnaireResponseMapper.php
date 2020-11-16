<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\QuestionnaireDto;
use App\Vendor\Entity\Questionnaire;

class QuestionnaireResponseMapper
{
    private QuestionResponseMapper $questionResponseMapper;

    public function __construct(QuestionResponseMapper $questionResponseMapper)
    {
        $this->questionResponseMapper = $questionResponseMapper;
    }

    public function map(Questionnaire $questionnaire): QuestionnaireDto
    {
        $questionnaireDto = new QuestionnaireDto();
        $questionnaireDto->id = $questionnaire->getId()->toString();
        $questionnaireDto->vendorId = $questionnaire->getVendor()->getId()->toString();
        $questionnaireDto->title = $questionnaire->getTitle() ?? '';
        $questionnaireDto->questions = $this->questionResponseMapper->mapMultiple(
            $questionnaire->getQuestions()->toArray()
        );

        return $questionnaireDto;
    }

    public function mapMultiple(array $questionnaires): array
    {
        $questionnaireDtos = [];

        foreach ($questionnaires as $questionnaire) {
            $questionnaireDtos[] = $this->map($questionnaire);
        }

        return $questionnaireDtos;
    }
}
