<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorSetting;
//use App\Vendor\Message\VendorSettingCreatedEvent;
//use App\Vendor\Message\VendorSettingDeletedEvent;
//use App\Vendor\Message\VendorSettingUpdatedEvent;
use App\Vendor\Repository\VendorSettingRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class VendorSettingManager
{
    private VendorSettingRepository $vendorSettingRepository;

    private MessageBusInterface $messageBus;

    public function __construct(
        VendorSettingRepository $vendorSettingRepository,
        MessageBusInterface $messageBus
    ) {
        $this->vendorSettingRepository = $vendorSettingRepository;
        $this->messageBus = $messageBus;
    }

    public function create(VendorSetting $vendorSetting)
    {
        $this->vendorSettingRepository->save($vendorSetting);

//        $this->messageBus->dispatch(new VendorSettingCreatedEvent($vendorSetting->getId()));
    }

    public function update(VendorSetting $vendorSetting)
    {
        $this->vendorSettingRepository->save($vendorSetting);

//        $this->messageBus->dispatch(new VendorSettingUpdatedEvent($vendorSetting->getId()));
    }

    public function delete(VendorSetting $vendorSetting)
    {
        $this->vendorSettingRepository->delete($vendorSetting);

//        $this->messageBus->dispatch(new VendorSettingDeletedEvent($vendorSetting->getId()));
    }
}
