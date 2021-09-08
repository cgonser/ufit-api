<?php

namespace App\Vendor\Provider;

use App\Subscription\Repository\SubscriptionRepository;
use App\Vendor\Dto\VendorStatsDto;
use App\Vendor\Dto\VendorStatsReceivableDto;
use App\Vendor\Dto\VendorStatsRevenueDto;
use App\Vendor\Dto\VendorStatsSubscriptionsDto;
use Ramsey\Uuid\UuidInterface;

class VendorStatsProvider
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
    ) {
    }

    public function getByVendor(UuidInterface $vendorId): VendorStatsDto
    {
        $vendorStatsDto = new VendorStatsDto();
        $vendorStatsDto->receivable = $this->generateReceivableStats($vendorId);
        $vendorStatsDto->subscriptions = $this->generateSubscriptionStats($vendorId);
        $vendorStatsDto->revenue = $this->generateRevenueStats($vendorId);

        return $vendorStatsDto;
    }

    private function generateReceivableStats(UuidInterface $vendorId): VendorStatsReceivableDto
    {
        $subscriptionStats = $this->getVendorReceivableStats($vendorId);

        $vendorStatsReceivableDto = new VendorStatsReceivableDto();
        $vendorStatsReceivableDto->currentAmount = $subscriptionStats['currentAmount'];
        $vendorStatsReceivableDto->nextPaymentAmount = $subscriptionStats['nextPaymentAmount'];
        $vendorStatsReceivableDto->nextPaymentDate = (new \DateTime())->format(\DateTimeInterface::ATOM);

        return $vendorStatsReceivableDto;
    }

    private function generateSubscriptionStats(UuidInterface $vendorId): VendorStatsSubscriptionsDto
    {
        $subscriptionStats = $this->getVendorSubscriptionStats($vendorId);

        $vendorStatsSubscriptionsDto = new VendorStatsSubscriptionsDto();
        $vendorStatsSubscriptionsDto->active = $subscriptionStats['active'];
        $vendorStatsSubscriptionsDto->new = $subscriptionStats['new'];
        $vendorStatsSubscriptionsDto->terminated = $subscriptionStats['terminated'];

        return $vendorStatsSubscriptionsDto;
    }

    private function generateRevenueStats(UuidInterface $vendorId): VendorStatsRevenueDto
    {
        $subscriptionStats = $this->getVendorReceivableStats($vendorId);

        $vendorStatsRevenueDto = new VendorStatsRevenueDto();
        $vendorStatsRevenueDto->currentAmount = $subscriptionStats['currentAmount'];
        $vendorStatsRevenueDto->previousAmount = '0.00';

        return $vendorStatsRevenueDto;
    }

    public function getVendorSubscriptionStats(UuidInterface $vendorId): array
    {
        return [
            'new' => count($this->subscriptionRepository->findActiveByVendor($vendorId)),
            'active' => count($this->subscriptionRepository->findActiveByVendor($vendorId)),
            'terminated' => count($this->subscriptionRepository->findInactiveByVendor($vendorId)),
        ];
    }

    public function getVendorRevenueStats(UuidInterface $vendorId): array
    {
        return $this->subscriptionRepository->getVendorReceivableStats($vendorId);
    }

    public function getVendorReceivableStats(UuidInterface $vendorId): array
    {
        return $this->subscriptionRepository->getVendorReceivableStats($vendorId);
    }
}