<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Questionnaire;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Repository\QuestionnaireRepository;
use App\Vendor\Request\QuestionnaireRequest;

class QuestionnaireService
{
    private QuestionnaireRepository $questionnaireRepository;

    private QuestionnaireProvider $questionnaireProvider;

    public function __construct(
        QuestionnaireRepository $questionnaireRepository,
        QuestionnaireProvider $questionnaireProvider
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionnaireProvider = $questionnaireProvider;
    }

    public function create(Vendor $vendor, QuestionnaireRequest $questionnaireRequest): Questionnaire
    {
        $questionnaire = new Questionnaire();
        $questionnaire->setVendor($vendor);
        $questionnaire->setTitle($questionnaireRequest->title);

        $this->questionnaireRepository->save($questionnaire);

        return $questionnaire;
    }

    public function update(Questionnaire $questionnaire, QuestionnaireRequest $questionnaireRequest)
    {
        $questionnaire->setTitle($questionnaireRequest->title);

        $this->questionnaireRepository->save($questionnaire);
    }

    public function delete(Questionnaire $questionnaire)
    {
        $this->questionnaireRepository->delete($questionnaire);
    }
}
