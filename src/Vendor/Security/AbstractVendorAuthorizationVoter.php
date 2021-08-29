<?php

declare(strict_types=1);

namespace App\Vendor\Security;

use App\Core\Security\AuthorizationVoterInterface;
use App\Vendor\Entity\Vendor;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\String\UnicodeString;

abstract class AbstractVendorAuthorizationVoter extends Voter implements AuthorizationVoterInterface
{
    abstract public function isSubjectSupported($subject): bool;

    public function supports(string $attribute, $subject): bool
    {
        if (! $this->isSubjectSupported($subject)) {
            return false;
        }

        return in_array($attribute, $this->getActionsHandled(), true);
    }

    public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $vendor = $token->getUser();

        if (! $vendor instanceof Vendor) {
            return false;
        }

        return match ($attribute) {
            self::CREATE => $this->canCreate($subject, $vendor),
            self::READ => $this->canRead($subject, $vendor),
            self::UPDATE => $this->canUpdate($subject, $vendor),
            self::DELETE => $this->canDelete($subject, $vendor),
            self::FIND => $this->canFind($subject, $vendor),
            default => $this->handleCustomAction($attribute, $subject, $vendor),
        };
    }

    protected function handleCustomAction(string $attribute, object $subject, Vendor $vendor): bool
    {
        $methodName = 'can'.ucfirst((new UnicodeString($attribute))->camel()->toString());

        if (! method_exists($this, $methodName)) {
            throw new LogicException('This code should not be reached!');
        }

        return $this->{$methodName}($subject, $vendor);
    }

    protected function getActionsHandled(): array
    {
        return [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::FIND];
    }

    protected function vendorCanModifyEntity(object $subject, Vendor $vendor): bool
    {
        return $vendor === $subject->getVendor();
    }

    protected function canCreate($subject, Vendor $vendor): bool
    {
        return true;
    }

    protected function canRead(object $subject, Vendor $vendor): bool
    {
        return $this->vendorCanModifyEntity($subject, $vendor);
    }

    protected function canUpdate(object $subject, Vendor $vendor): bool
    {
        return $this->vendorCanModifyEntity($subject, $vendor);
    }

    protected function canDelete(object $subject, Vendor $vendor): bool
    {
        return $this->vendorCanModifyEntity($subject, $vendor);
    }

    protected function canFind($subject, Vendor $vendor): bool
    {
        return true;
    }
}
