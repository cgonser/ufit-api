<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Question;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\QuestionProvider;
use App\Vendor\Repository\QuestionnaireRepository;
use App\Vendor\Repository\QuestionRepository;
use App\Vendor\Request\QuestionnaireRequest;
use App\Vendor\Request\QuestionRequest;
use Ramsey\Uuid\Uuid;

class QuestionnaireService
{
    private QuestionnaireRepository $questionnaireRepository;

    private QuestionnaireProvider $questionnaireProvider;

    private QuestionProvider $questionProvider;

    private QuestionRepository $questionRepository;

    public function __construct(
        QuestionnaireRepository $questionnaireRepository,
        QuestionRepository $questionRepository,
        QuestionnaireProvider $questionnaireProvider,
        QuestionProvider $questionProvider
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionnaireProvider = $questionnaireProvider;
        $this->questionProvider = $questionProvider;
        $this->questionRepository = $questionRepository;
    }

    public function create(Vendor $vendor, QuestionnaireRequest $questionnaireRequest): Questionnaire
    {
        $questionnaire = new Questionnaire();
        $questionnaire->setVendor($vendor);

        $this->mapFromRequest($questionnaire, $questionnaireRequest);

        $this->questionnaireRepository->save($questionnaire);

        return $questionnaire;
    }

    public function update(Questionnaire $questionnaire, QuestionnaireRequest $questionnaireRequest)
    {
        $this->mapFromRequest($questionnaire, $questionnaireRequest);

        $this->questionnaireRepository->save($questionnaire);
    }

    public function delete(Questionnaire $questionnaire)
    {
        $this->questionnaireRepository->delete($questionnaire);
    }

    private function mapFromRequest(Questionnaire $questionnaire, QuestionnaireRequest $questionnaireRequest)
    {
        $questionnaire->setTitle($questionnaireRequest->title);

        if (count($questionnaireRequest->questions) > 0) {
            $this->mapItemsFromRequest($questionnaire, $questionnaireRequest->questions);
        }
    }

    private function mapItemsFromRequest(Questionnaire $questionnaire, array $questions)
    {
        /** @var QuestionRequest $questionRequest */
        foreach ($questions as $questionRequest) {
            $question = null;

            if (null !== $questionRequest->id) {
                $question = $this->questionProvider->findOneByQuestionnaireAndId(
                    $questionnaire, Uuid::fromString($questionRequest->id)
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
