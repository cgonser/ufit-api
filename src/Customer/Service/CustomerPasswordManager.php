<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Core\Service\EmailComposer;
use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerPasswordResetToken;
use App\Customer\Exception\CustomerInvalidPasswordException;
use App\Customer\Exception\CustomerPasswordResetTokenExpiredException;
use App\Customer\Exception\CustomerPasswordResetTokenNotFoundException;
use App\Customer\Repository\CustomerPasswordResetTokenRepository;
use App\Customer\Repository\CustomerRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerPasswordManager
{
    private const TOKEN_VALIDITY = '60 minutes';

    private UserPasswordHasherInterface $userPasswordHasher;

    private CustomerRepository $customerRepository;

    private CustomerPasswordResetTokenRepository $customerPasswordResetTokenRepository;

    private EmailComposer $emailComposer;

    private MailerInterface $mailer;

    private string $customerPasswordResetUrl;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        CustomerRepository $customerRepository,
        CustomerPasswordResetTokenRepository $customerPasswordResetTokenRepository,
        EmailComposer $emailComposer,
        MailerInterface $mailer,
        string $customerPasswordResetUrl
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->customerRepository = $customerRepository;
        $this->customerPasswordResetTokenRepository = $customerPasswordResetTokenRepository;
        $this->emailComposer = $emailComposer;
        $this->mailer = $mailer;
        $this->customerPasswordResetUrl = $customerPasswordResetUrl;
    }

    public function changePassword(Customer $customer, string $currentPassword, string $newPassword): void
    {
        if (null !== $customer->getPassword()) {
            $isPasswordValid = $this->userPasswordHasher->isPasswordValid($customer, $currentPassword);

            if (! $isPasswordValid) {
                throw new CustomerInvalidPasswordException();
            }
        }

        $customer->setPassword($this->userPasswordHasher->hashPassword($customer, $newPassword));

        $this->customerRepository->save($customer);
    }

    public function hashPassword(Customer $customer, string $password): string
    {
        return $this->userPasswordHasher->hashPassword($customer, $password);
    }

    public function startPasswordReset(Customer $customer): void
    {
        $expiresAt = new \DateTime();
        $expiresAt->modify('+'.self::TOKEN_VALIDITY);

        $customerPasswordResetToken = new CustomerPasswordResetToken();
        $customerPasswordResetToken->setCustomer($customer);
        $customerPasswordResetToken->setExpiresAt($expiresAt);
        $customerPasswordResetToken->setToken($this->generateResetToken($customerPasswordResetToken));

        $this->customerPasswordResetTokenRepository->save($customerPasswordResetToken);

        $this->sendResetEmail($customerPasswordResetToken);
    }

    public function resetPassword(Customer $customer, string $token, string $password): void
    {
        $customerPasswordResetToken = $this->customerPasswordResetTokenRepository->findOneBy([
            'customer' => $customer,
            'token' => $token,
        ]);

        if (! $customerPasswordResetToken) {
            throw new CustomerPasswordResetTokenNotFoundException();
        }

        if ((new \DateTime()) > $customerPasswordResetToken->getExpiresAt()) {
            throw new CustomerPasswordResetTokenExpiredException();
        }

        $customer->setPassword($this->userPasswordHasher->hashPassword($customer, $password));

        $this->customerRepository->save($customer);

        $this->customerPasswordResetTokenRepository->delete($customerPasswordResetToken);
    }

    private function sendResetEmail(CustomerPasswordResetToken $customerPasswordResetToken): void
    {
        $customer = $customerPasswordResetToken->getCustomer();

        $resetUrl = strtr(
            $this->customerPasswordResetUrl,
            [
                '%token%' => base64_encode($customer->getUsername().'|'.$customerPasswordResetToken->getToken()),
            ]
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'customer.password_reset',
                [
                    $customer->getName() => $customer->getEmail(),
                ],
                [
                    'greeting_name' => $customer->getName(),
                    'reset_url' => $resetUrl,
                ],
                $customer->getLocale()
            )
        );
    }

    private function generateResetToken(CustomerPasswordResetToken $customerPasswordResetToken): string
    {
        $plainToken = $customerPasswordResetToken->getCustomer()
            ->getId()
            ->toString();
        $plainToken .= '|'.Uuid::uuid4();
        $plainToken .= '|'.$customerPasswordResetToken->getExpiresAt()->getTimestamp();

        return $this->hashPassword($customerPasswordResetToken->getCustomer(), $plainToken);
    }
}
