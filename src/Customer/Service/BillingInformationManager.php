<?php

namespace App\Customer\Service;

use App\Customer\Entity\BillingInformation;
use App\Customer\Repository\BillingInformationRepository;

class BillingInformationManager
{
    private BillingInformationRepository $billingInformationRepository;

    public function __construct(
        BillingInformationRepository $billingInformationRepository
    ) {
        $this->billingInformationRepository = $billingInformationRepository;
    }

    public function create(BillingInformation $billingInformation): void
    {
        $this->billingInformationRepository->save($billingInformation);
    }

    public function update(BillingInformation $billingInformation): void
    {
        $this->billingInformationRepository->save($billingInformation);
    }

    public function delete(BillingInformation $billingInformation): void
    {
        $this->billingInformationRepository->delete($billingInformation);
    }
}
