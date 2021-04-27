<?php

namespace App\Payment\Service\PaymentProcessor;

use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Service\VendorSettingManager;
use PagarMe\Client;
use Ramsey\Uuid\UuidInterface;

class PagarmeVendorInformationManager implements VendorInformationManagerInterface
{
    private VendorBankAccountProvider $vendorBankAccountProvider;

    private VendorSettingManager $vendorSettingManager;

    private Client $pagarmeClient;

    public function __construct(
        VendorBankAccountProvider $vendorBankAccountProvider,
        VendorSettingManager $vendorSettingManager,
        Client $pagarmeClient
    ) {
        $this->vendorBankAccountProvider = $vendorBankAccountProvider;
        $this->vendorSettingManager = $vendorSettingManager;
        $this->pagarmeClient = $pagarmeClient;
    }

    public function updateVendorInformation(UuidInterface $vendorId)
    {
        $vendorBankAccount = $this->vendorBankAccountProvider->getOneByVendorId($vendorId);

        $this->pushVendorInformation($vendorBankAccount);
    }

    public function pushVendorInformation(VendorBankAccount $vendorBankAccount)
    {
        $pagarmeId = $this->vendorSettingManager->getValue($vendorBankAccount->getVendorId(), 'pagarme_id');
        $transferInterval = $this->vendorSettingManager->getValue($vendorBankAccount->getVendorId(), 'pagarme_transfer_interval', 'weekly');
        $transferDay = $this->vendorSettingManager->getValue($vendorBankAccount->getVendorId(), 'pagarme_transfer_day', '1');

        $recipientData = [
            'transfer_enabled' => 'true',
            'transfer_day' => $transferDay,
            'transfer_interval' => $transferInterval,
            'bank_account' => [
                'bank_code' => $vendorBankAccount->getBankCode(),
                'agencia' => $vendorBankAccount->getAgencyNumber(),
//                'agencia_dv' => '',
                'conta' => $vendorBankAccount->getAccountNumber(),
                'conta_dv' => $vendorBankAccount->getAccountDigit(),
                'document_number' => $vendorBankAccount->getOwnerDocumentNumber(),
                'legal_name' => $vendorBankAccount->getOwnerName(),
            ],
        ];

        if (null !== $pagarmeId) {
            $recipientData['id'] = $pagarmeId;

            $this->pagarmeClient->recipients()->update($recipientData);
        } else {
            $recipient = $this->pagarmeClient->recipients()->create($recipientData);

            $this->vendorSettingManager->set($vendorBankAccount->getVendorId(), 'pagarme_id', $recipient->id);
        }
    }
}
