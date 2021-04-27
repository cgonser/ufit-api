<?php

namespace App\Vendor\Service;

use App\Core\Service\EmailComposer;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPasswordResetToken;
use App\Vendor\Exception\VendorInvalidPasswordException;
use App\Vendor\Exception\VendorPasswordResetTokenExpiredException;
use App\Vendor\Exception\VendorPasswordResetTokenNotFoundException;
use App\Vendor\Repository\VendorPasswordResetTokenRepository;
use App\Vendor\Repository\VendorRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class VendorPasswordManager
{
    private const TOKEN_VALIDITY = '60 minutes';

    private UserPasswordEncoderInterface $userPasswordEncoder;

    private VendorRepository $vendorRepository;

    private VendorPasswordResetTokenRepository $vendorPasswordResetTokenRepository;

    private EmailComposer $emailComposer;

    private MailerInterface $mailer;

    private string $vendorPasswordResetUrl;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        VendorRepository $vendorRepository,
        VendorPasswordResetTokenRepository $vendorPasswordResetTokenRepository,
        EmailComposer $emailComposer,
        MailerInterface $mailer,
        string $vendorPasswordResetUrl
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->vendorRepository = $vendorRepository;
        $this->vendorPasswordResetTokenRepository = $vendorPasswordResetTokenRepository;
        $this->emailComposer = $emailComposer;
        $this->mailer = $mailer;
        $this->vendorPasswordResetUrl = $vendorPasswordResetUrl;
    }

    public function changePassword(Vendor $vendor, string $currentPassword, string $newPassword)
    {
        if (null !== $vendor->getPassword()) {
            $isPasswordValid = $this->userPasswordEncoder->isPasswordValid($vendor, $currentPassword);

            if (!$isPasswordValid) {
                throw new VendorInvalidPasswordException();
            }
        }

        $vendor->setPassword(
            $this->userPasswordEncoder->encodePassword($vendor, $newPassword)
        );

        $this->vendorRepository->save($vendor);
    }

    public function encodePassword(Vendor $vendor, string $password): string
    {
        return $this->userPasswordEncoder->encodePassword($vendor, $password);
    }

    public function startPasswordReset(Vendor $vendor): void
    {
        $expiresAt = new \DateTime();
        $expiresAt->modify('+'.self::TOKEN_VALIDITY);

        $vendorPasswordResetToken = new VendorPasswordResetToken();
        $vendorPasswordResetToken->setVendor($vendor);
        $vendorPasswordResetToken->setExpiresAt($expiresAt);
        $vendorPasswordResetToken->setToken($this->generateResetToken($vendorPasswordResetToken));

        $this->vendorPasswordResetTokenRepository->save($vendorPasswordResetToken);

        $this->sendResetEmail($vendorPasswordResetToken);
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
        $plainToken = $vendorPasswordResetToken->getVendor()->getId()->toString();
        $plainToken .= '|'.Uuid::uuid4();
        $plainToken .= '|'.$vendorPasswordResetToken->getExpiresAt()->getTimestamp();

        return $this->encodePassword($vendorPasswordResetToken->getVendor(), $plainToken);
    }

    public function resetPassword(Vendor $vendor, string $token, string $password)
    {
        $vendorPasswordResetToken = $this->vendorPasswordResetTokenRepository->findOneBy([
            'vendor' => $vendor,
            'token' => $token,
        ]);

        if (!$vendorPasswordResetToken) {
            throw new VendorPasswordResetTokenNotFoundException();
        }

        if ((new \DateTime()) > $vendorPasswordResetToken->getExpiresAt()) {
            throw new VendorPasswordResetTokenExpiredException();
        }

        $vendor->setPassword(
            $this->userPasswordEncoder->encodePassword($vendor, $password)
        );

        $this->vendorRepository->save($vendor);

        $this->vendorPasswordResetTokenRepository->delete($vendorPasswordResetToken);
    }
}
