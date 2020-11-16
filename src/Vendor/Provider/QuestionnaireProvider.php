<?php

namespace App\Vendor\Provider;

use App\Vendor\Entity\Questionnaire;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Repository\QuestionnaireRepository;
use Ramsey\Uuid\UuidInterface;

class QuestionnaireProvider
{
    private QuestionnaireRepository $questionnaireRepository;

    public function __construct(QuestionnaireRepository $questionnaireRepository)
    {
        $this->questionnaireRepository = $questionnaireRepository;
    }

    public function get(UuidInterface $questionnaireId): Questionnaire
    {
        /** @var Questionnaire|null $questionnaire */
        $questionnaire = $this->questionnaireRepository->find($questionnaireId);

        if (!$questionnaire) {
            throw new QuestionnaireNotFoundException();
        }

        return $questionnaire;
    }

    public function getByVendorAndId(Vendor $vendor, UuidInterface $questionnaireId): Questionnaire
    {
        /** @var Questionnaire|null $vendorPlan */
        $questionnaire = $this->questionnaireRepository->findOneBy([
            'id' => $questionnaireId,
            'vendor' => $vendor,
        ]);

        if (!$questionnaire) {
            throw new QuestionnaireNotFoundException();
        }

        return $questionnaire;
    }

    public function findByVendor(Vendor $vendor): array
    {
        return $this->questionnaireRepository->findBy(['vendor' => $vendor]);
    }
}
