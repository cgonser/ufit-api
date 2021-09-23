<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\Question;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\QuestionProvider;
use App\Vendor\Repository\QuestionnaireRepository;
use App\Vendor\Request\QuestionnaireRequest;
use App\Vendor\Request\QuestionRequest;
use Ramsey\Uuid\Uuid;

class QuestionnaireService
{
    public function __construct(
        private QuestionnaireRepository $questionnaireRepository,
        private QuestionProvider $questionProvider
    ) {
    }

    public function create(Vendor $vendor, QuestionnaireRequest $questionnaireRequest): Questionnaire
    {
        $questionnaire = new Questionnaire();
        $questionnaire->setVendor($vendor);

        $this->mapFromRequest($questionnaire, $questionnaireRequest);

        $this->questionnaireRepository->save($questionnaire);

        return $questionnaire;
    }

    public function update(Questionnaire $questionnaire, QuestionnaireRequest $questionnaireRequest): void
    {
        $this->mapFromRequest($questionnaire, $questionnaireRequest);

        $this->questionnaireRepository->save($questionnaire);
    }

    public function delete(Questionnaire $questionnaire): void
    {
        $this->questionnaireRepository->delete($questionnaire);
    }

    private function mapFromRequest(Questionnaire $questionnaire, QuestionnaireRequest $questionnaireRequest): void
    {
        $questionnaire->setTitle($questionnaireRequest->title);

        if ([] !== $questionnaireRequest->questions) {
            $this->mapItemsFromRequest($questionnaire, $questionnaireRequest->questions);
        }
    }

    private function mapItemsFromRequest(Questionnaire $questionnaire, array $questions): void
    {
        /** @var QuestionRequest $questionRequest */
        foreach ($questions as $questionRequest) {
            $question = null;

            if (null !== $questionRequest->id) {
                $question = $this->questionProvider->findOneByQuestionnaireAndId(
                    $questionnaire,
                    Uuid::fromString($questionRequest->id)
                );
            }

            if (null === $question) {
                $question = new Question();
            }

            $question->setQuestion($questionRequest->question);
            $question->setOrder($questionRequest->order);

            $questionnaire->addQuestion($question);
        }
    }
}
