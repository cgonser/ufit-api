<?php

declare(strict_types=1);

namespace App\Vendor\Provider;

use App\Core\Provider\AbstractProvider;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\QuestionnaireNotFoundException;
use App\Vendor\Repository\QuestionnaireRepository;
use Ramsey\Uuid\UuidInterface;

class QuestionnaireProvider extends AbstractProvider
{
    public function __construct(QuestionnaireRepository $questionnaireRepository) {
        $this->repository = $questionnaireRepository;
    }

    public function getByVendorAndId(Vendor $vendor, UuidInterface $questionnaireId): Questionnaire
    {
        $questionnaire = $this->repository->findOneBy([
            'id' => $questionnaireId,
            'vendor' => $vendor,
        ]);

        if (! $questionnaire) {
            throw new QuestionnaireNotFoundException();
        }

        return $questionnaire;
    }

    public function findByVendor(Vendor $vendor): array
    {
        return $this->repository->findBy([
            'vendor' => $vendor,
        ]);
    }

    protected function throwNotFoundException(): void
    {
        throw new QuestionnaireNotFoundException();
    }

    /**
     * @return string[]
     */
    protected function getFilterableFields(): array
    {
        return ['vendorId'];
    }
}
