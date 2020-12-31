<?php

namespace App\Vendor\Service;

use App\Core\Provider\CurrencyProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Exception\VendorPlanSlugInUseException;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Provider\VendorPlanProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorPlanRepository;
use App\Vendor\Request\VendorPlanRequest;
use Ramsey\Uuid\Uuid;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorPlanService
{
    private VendorPlanRepository $vendorPlanRepository;

    private VendorPlanProvider $vendorPlanProvider;

    private VendorProvider $vendorProvider;

    private QuestionnaireProvider $questionnaireProvider;

    private CurrencyProvider $currencyProvider;

    private SluggerInterface $slugger;

    public function __construct(
        VendorPlanRepository $vendorPlanRepository,
        VendorPlanProvider $vendorPlanProvider,
        VendorProvider $vendorProvider,
        QuestionnaireProvider $questionnaireProvider,
        CurrencyProvider $currencyProvider,
        SluggerInterface $slugger
    ) {
        $this->vendorPlanRepository = $vendorPlanRepository;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->vendorProvider = $vendorProvider;
        $this->questionnaireProvider = $questionnaireProvider;
        $this->currencyProvider = $currencyProvider;
        $this->slugger = $slugger;
    }

    public function create(Vendor $vendor, VendorPlanRequest $vendorPlanRequest): VendorPlan
    {
        $vendorPlan = new VendorPlan();
        $vendorPlan->setVendor($vendor);

        $this->mapFromRequest($vendorPlan, $vendorPlanRequest);

        $this->vendorPlanRepository->save($vendorPlan);

        return $vendorPlan;
    }

    public function update(VendorPlan $vendorPlan, VendorPlanRequest $vendorPlanRequest)
    {
        $this->mapFromRequest($vendorPlan, $vendorPlanRequest);

        $this->vendorPlanRepository->save($vendorPlan);
    }

    private function mapFromRequest(VendorPlan $vendorPlan, VendorPlanRequest $vendorPlanRequest)
    {
        $vendorPlan->setName($vendorPlanRequest->name);
        $vendorPlan->setDescription($vendorPlanRequest->description);
        $vendorPlan->setFeatures($vendorPlanRequest->features);
        $vendorPlan->setPrice($vendorPlanRequest->price);
        $vendorPlan->setCurrency($this->currencyProvider->getByCode($vendorPlanRequest->currency));
        $vendorPlan->setDuration(
            $this->prepareDuration($vendorPlanRequest->durationMonths, $vendorPlanRequest->durationDays)
        );
        $vendorPlan->setIsApprovalRequired($vendorPlanRequest->isApprovalRequired);

        if (null !== $vendorPlanRequest->slug) {
            if (!$this->isSlugUnique($vendorPlan, $vendorPlanRequest->slug)) {
                throw new VendorPlanSlugInUseException();
            }

            $vendorPlan->setSlug($vendorPlanRequest->slug);
        } elseif (null === $vendorPlan->getSlug()) {
            $vendorPlan->setSlug($this->generateSlug($vendorPlan));
        }

        if (null !== $vendorPlanRequest->questionnaireId) {
            $questionnaire = $this->questionnaireProvider->getByVendorAndId(
                $vendorPlan->getVendor(),
                Uuid::fromString($vendorPlanRequest->questionnaireId)
            );

            $vendorPlan->setQuestionnaire($questionnaire);
        } else {
            $vendorPlan->setQuestionnaire(null);
        }
    }

    private function prepareDuration(string $durationMonths, string $durationDays): \DateInterval
    {
        $durationString = 'P';

        if (null !== $durationMonths && $durationMonths > 0) {
            $durationString .= sprintf('%sM', $durationMonths);
        }
        if (null !== $durationDays && $durationDays > 0) {
            $durationString .= sprintf('%sD', $durationDays);
        }

        try {
            return new \DateInterval($durationString);
        } catch (\Exception $e) {
            throw new VendorPlanInvalidDurationException();
        }
    }

    public function delete(VendorPlan $vendorPlan)
    {
        $this->vendorPlanRepository->delete($vendorPlan);
    }

    public function generateSlug(VendorPlan $vendorPlan, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($vendorPlan->getName()));

        if (null !== $suffix) {
            $slug .= '-'.(string) $suffix;
        }

        if ($this->isSlugUnique($vendorPlan, $slug)) {
            return $slug;
        }

        $suffix = null !== $suffix ? $suffix + 1 : 1;

        return $this->generateSlug($vendorPlan, $suffix);
    }

    private function isSlugUnique(VendorPlan $vendorPlan, string $slug): bool
    {
        $existingVendorPlan = $this->vendorPlanProvider->findOneByVendorAndSlug($vendorPlan->getVendor(), $slug);

        if (!$existingVendorPlan) {
            return true;
        }

        if (!$vendorPlan->isNew() && $existingVendorPlan->getId()->toString() == $vendorPlan->getId()->toString()) {
            return true;
        }

        return false;
    }
}
