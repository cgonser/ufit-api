<?php

declare(strict_types=1);

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Exception\PagarmeInvalidInputException;
use App\Payment\Service\PaymentProcessor\VendorInformationManagerInterface;
use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Service\VendorBankAccountManager;
use App\Vendor\Service\VendorSettingManager;
use PagarMe\Client;
use PagarMe\Exceptions\PagarMeException;
use Ramsey\Uuid\UuidInterface;

class PagarmeVendorInformationManager implements VendorInformationManagerInterface
{
    /**
     * @var string
     */
    public const PAGARME_ERROR_SAME_DOC = 'ERROR TYPE: invalid_parameter. PARAMETER: bank_account_id. MESSAGE: The new bank account should have the same document number as the previous';

    public function __construct(private VendorBankAccountProvider $vendorBankAccountProvider, private VendorBankAccountManager $vendorBankAccountManager, private VendorSettingManager $vendorSettingManager, private Client $pagarmeClient)
    {
    }

    public function updateVendorInformation(UuidInterface $vendorId): void
    {
        $vendorBankAccount = $this->vendorBankAccountProvider->getOneByVendorId($vendorId);

        $this->pushVendorInformation($vendorBankAccount);
    }

    /**
     * @return mixed|void
     */
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

                $this->pagarmeClient->recipients()
                    ->update($recipientData);
            } else {
                $arrayObject = $this->pagarmeClient->recipients()
                    ->create($recipientData);

                $this->vendorSettingManager->set($vendorBankAccount->getVendorId(), 'pagarme_id', $arrayObject->id);
            }

            $this->vendorBankAccountManager->markAsValid($vendorBankAccount);
        } catch (PagarMeException $pagarMeException) {
            if (self::PAGARME_ERROR_SAME_DOC === $pagarMeException->getMessage()) {
                $this->vendorSettingManager->set($vendorBankAccount->getVendorId(), 'pagarme_id', null);

                return $this->pushVendorInformation($vendorBankAccount);
            }

            $this->handlePagarmeException($vendorBankAccount, $pagarMeException);
        }
    }

    private function handlePagarmeException(VendorBankAccount $vendorBankAccount, PagarMeException $pagarMeException): void
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

        $propertyName = $mappedProperties[$pagarMeException->getParameterName()] ?? $pagarMeException->getParameterName();

        throw new PagarmeInvalidInputException($vendorBankAccount, $propertyName, $pagarMeException->getMessage());
    }
}
