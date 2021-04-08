<?php

namespace App\Vendor\Service;

use App\Core\Provider\CurrencyProvider;
use App\Core\Provider\PaymentMethodProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Provider\QuestionnaireProvider;
use App\Vendor\Request\VendorPlanRequest;
use Decimal\Decimal;
use Ramsey\Uuid\Uuid;

class VendorPlanRequestManager
{
    private VendorPlanManager $vendorPlanManager;

    private VendorPlanImageService $vendorPlanImageService;

    private QuestionnaireProvider $questionnaireProvider;

    private PaymentMethodProvider $paymentMethodProvider;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        VendorPlanManager $vendorPlanManager,
        VendorPlanImageService $vendorPlanImageService,
        QuestionnaireProvider $questionnaireProvider,
        CurrencyProvider $currencyProvider,
        PaymentMethodProvider $paymentMethodProvider
    ) {
        $this->vendorPlanManager = $vendorPlanManager;
        $this->vendorPlanImageService = $vendorPlanImageService;
        $this->questionnaireProvider = $questionnaireProvider;
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->currencyProvider = $currencyProvider;
    }

    public function createFromRequest(Vendor $vendor, VendorPlanRequest $vendorPlanRequest): VendorPlan
    {
        $vendorPlan = new VendorPlan();
        $vendorPlan->setVendor($vendor);

        $this->mapFromRequest($vendorPlan, $vendorPlanRequest);

        $this->vendorPlanManager->create($vendorPlan);

        return $vendorPlan;
    }

    public function updateFromRequest(VendorPlan $vendorPlan, VendorPlanRequest $vendorPlanRequest): void
    {
        $this->mapFromRequest($vendorPlan, $vendorPlanRequest);

        $this->vendorPlanManager->update($vendorPlan);
    }

    private function mapFromRequest(VendorPlan $vendorPlan, VendorPlanRequest $vendorPlanRequest): void
    {
        if (null !== $vendorPlanRequest->name) {
            $vendorPlan->setName($vendorPlanRequest->name);
        }

        if (null !== $vendorPlanRequest->description) {
            $vendorPlan->setDescription($vendorPlanRequest->description);
        }

        if (null !== $vendorPlanRequest->features) {
            $vendorPlan->setFeatures($vendorPlanRequest->features);
        }

        if (null !== $vendorPlanRequest->price) {
            $vendorPlan->setPrice(new Decimal($vendorPlanRequest->price));
        }

        if (null !== $vendorPlanRequest->currency) {
            $vendorPlan->setCurrency($this->currencyProvider->getByCode($vendorPlanRequest->currency));
        }

        if (null !== $vendorPlanRequest->isApprovalRequired) {
            $vendorPlan->setIsApprovalRequired($vendorPlanRequest->isApprovalRequired);
        }

        if (null !== $vendorPlanRequest->isRecurring) {
            $vendorPlan->setIsRecurring($vendorPlanRequest->isRecurring);
        }

        if (null !== $vendorPlanRequest->durationMonths || null !== $vendorPlanRequest->durationDays) {
            $vendorPlan->setDuration(
                $this->prepareDuration($vendorPlanRequest->durationMonths, $vendorPlanRequest->durationDays)
            );
        }

        if (null !== $vendorPlanRequest->paymentMethods) {
            $vendorPlan->getPaymentMethods()->clear();

            foreach ($vendorPlanRequest->paymentMethods as $paymentMethodId) {
                $vendorPlan->addPaymentMethod(
                    $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId))
                );
            }
        }

        if (null !== $vendorPlanRequest->isVisible) {
            $vendorPlan->setIsVisible($vendorPlanRequest->isVisible);
        }

        if (null !== $vendorPlanRequest->slug) {
            $vendorPlan->setSlug($vendorPlanRequest->slug);
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

        if (null !== $vendorPlanRequest->imageContents) {
            $this->vendorPlanImageService->uploadImage($vendorPlan, $vendorPlanRequest->imageContents);
        }
    }

    private function prepareDuration(?string $durationMonths, ?string $durationDays): \DateInterval
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
}
