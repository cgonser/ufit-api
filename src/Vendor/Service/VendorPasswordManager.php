<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Core\Service\EmailComposer;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPasswordResetToken;
use App\Vendor\Exception\VendorInvalidPasswordException;
use App\Vendor\Exception\VendorPasswordResetTokenExpiredException;
use App\Vendor\Exception\VendorPasswordResetTokenNotFoundException;
use App\Vendor\Repository\VendorPasswordResetTokenRepository;
use App\Vendor\Repository\VendorRepository;
use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class VendorPasswordManager
{
    /**
     * @var string
     */
    private const TOKEN_VALIDITY = '60 minutes';

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private VendorRepository $vendorRepository,
        private VendorPasswordResetTokenRepository $vendorPasswordResetTokenRepository,
        private EmailComposer $emailComposer,
        private MailerInterface $mailer,
        private string $vendorPasswordResetUrl
    ) {
    }

    public function changePassword(Vendor $vendor, string $currentPassword, string $newPassword): void
    {
        if (null !== $vendor->getPassword()) {
            $isPasswordValid = $this->userPasswordHasher->isPasswordValid($vendor, $currentPassword);

            if (! $isPasswordValid) {
                throw new VendorInvalidPasswordException();
            }
        }

        $vendor->setPassword($this->userPasswordHasher->hashPassword($vendor, $newPassword));

        $this->vendorRepository->save($vendor);
    }

    public function encodePassword(Vendor $vendor, string $password): string
    {
        return $this->userPasswordHasher->hashPassword($vendor, $password);
    }

    public function startPasswordReset(Vendor $vendor): void
    {
        $dateTime = new DateTime();
        $dateTime->modify('+'.self::TOKEN_VALIDITY);

        $vendorPasswordResetToken = new VendorPasswordResetToken();
        $vendorPasswordResetToken->setVendor($vendor);
        $vendorPasswordResetToken->setExpiresAt($dateTime);
        $vendorPasswordResetToken->setToken($this->generateResetToken($vendorPasswordResetToken));

        $this->vendorPasswordResetTokenRepository->save($vendorPasswordResetToken);

        $this->sendResetEmail($vendorPasswordResetToken);
    }

    public function resetPassword(Vendor $vendor, string $token, string $password): void
    {
        $vendorPasswordResetToken = $this->vendorPasswordResetTokenRepository->findOneBy([
            'vendor' => $vendor,
            'token' => $token,
        ]);

        if (! $vendorPasswordResetToken) {
            throw new VendorPasswordResetTokenNotFoundException();
        }

        if ((new DateTime()) > $vendorPasswordResetToken->getExpiresAt()) {
            throw new VendorPasswordResetTokenExpiredException();
        }

        $vendor->setPassword($this->userPasswordHasher->hashPassword($vendor, $password));

        $this->vendorRepository->save($vendor);

        $this->vendorPasswordResetTokenRepository->delete($vendorPasswordResetToken);
    }

    private function sendResetEmail(VendorPasswordResetToken $vendorPasswordResetToken): void
    {
        $vendor = $vendorPasswordResetToken->getVendor();

        $resetUrl = strtr(
            $this->vendorPasswordResetUrl,
            [
                '%token%' => base64_encode($vendor->getUsername().'|'.$vendorPasswordResetToken->getToken()),
            ]
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'vendor.password_reset',
                [
                    $vendor->getName() => $vendor->getEmail(),
                ],
                [
                    'greeting_name' => $vendor->getName(),
                    'reset_url' => $resetUrl,
                ],
                $vendor->getLocale()
            )
        );
    }

    private function generateResetToken(VendorPasswordResetToken $vendorPasswordResetToken): string
    {
        $plainToken = $vendorPasswordResetToken->getVendor()
            ->getId()
            ->toString();
        $plainToken .= '|'.Uuid::uuid4();
        $plainToken .= '|'.$vendorPasswordResetToken->getExpiresAt()->getTimestamp();

        return $this->encodePassword($vendorPasswordResetToken->getVendor(), $plainToken);
    }
}
