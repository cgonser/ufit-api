<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Service\PaymentProcessor\VendorInformationManagerInterface;
use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Exception\VendorBankAccountInvalidException;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Service\VendorBankAccountManager;
use App\Vendor\Service\VendorSettingManager;
use PagarMe\Client;
use PagarMe\Exceptions\PagarMeException;
use Ramsey\Uuid\UuidInterface;

class PagarmeVendorInformationManager implements VendorInformationManagerInterface
{
    private VendorBankAccountProvider $vendorBankAccountProvider;

    private VendorBankAccountManager $vendorBankAccountManager;

    private VendorSettingManager $vendorSettingManager;

    private Client $pagarmeClient;

    public function __construct(
        VendorBankAccountProvider $vendorBankAccountProvider,
        VendorBankAccountManager $vendorBankAccountManager,
        VendorSettingManager $vendorSettingManager,
        Client $pagarmeClient
    ) {
        $this->vendorBankAccountProvider = $vendorBankAccountProvider;
        $this->vendorBankAccountManager = $vendorBankAccountManager;
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
        $transferInterval = $this->vendorSettingManager->getValue(
            $vendorBankAccount->getVendorId(),
            'pagarme_transfer_interval',
            'weekly'
        );
        $transferDay = $this->vendorSettingManager->getValue(
            $vendorBankAccount->getVendorId(),
            'pagarme_transfer_day',
            '1'
        );

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

        try {
            if (null !== $pagarmeId) {
                $recipientData['id'] = $pagarmeId;

                $this->pagarmeClient->recipients()->update($recipientData);
            } else {
                $recipient = $this->pagarmeClient->recipients()->create($recipientData);

                $this->vendorSettingManager->set($vendorBankAccount->getVendorId(), 'pagarme_id', $recipient->id);
            }

            $this->vendorBankAccountManager->markAsValid($vendorBankAccount);
        } catch (PagarMeException $e) {
            $this->handlePagarmeException($vendorBankAccount, $e);
        }
    }

    private function handlePagarmeException(VendorBankAccount $vendorBankAccount, PagarMeException $e): void
    {
        $this->vendorBankAccountManager->markAsInvalid($vendorBankAccount);

        $mappedProperties = [
            'bank_code' => 'bankCode',
            'agencia' => 'agencyNumber',
            'conta' => 'accountNumber',
            'conta_dv' => 'accountDigit',
            'document_number' => 'ownerDocumentNumber',
            'legal_name' => 'ownerName',
        ];

        $propertyName = $mappedProperties[$e->getParameterName()] ?? $e->getParameterName();

        throw new VendorBankAccountInvalidException($vendorBankAccount, $propertyName, $e->getMessage());
    }
}
