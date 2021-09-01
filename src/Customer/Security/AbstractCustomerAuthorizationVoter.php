<?php

declare(strict_types=1);

namespace App\Customer\Security;

use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\String\UnicodeString;

abstract class AbstractCustomerAuthorizationVoter extends Voter implements AuthorizationVoterInterface
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
        $customer = $token->getUser();

        if (! $customer instanceof Customer) {
            return false;
        }

        return match ($attribute) {
            self::CREATE => $this->canCreate($subject, $customer),
            self::READ => $this->canRead($subject, $customer),
            self::UPDATE => $this->canUpdate($subject, $customer),
            self::DELETE => $this->canDelete($subject, $customer),
            self::FIND => $this->canFind($subject, $customer),
            default => $this->handleCustomAction($attribute, $subject, $customer),
        };
    }

    protected function handleCustomAction(string $attribute, object $subject, Customer $customer): bool
    {
        $methodName = 'can'.ucfirst((new UnicodeString($attribute))->camel()->toString());

        if (! method_exists($this, $methodName)) {
            throw new LogicException('This code should not be reached!');
        }

        return $this->{$methodName}($subject, $customer);
    }

    protected function getActionsHandled(): array
    {
        return [self::CREATE, self::READ, self::UPDATE, self::DELETE, self::FIND];
    }

    protected function customerCanModifyEntity(object $subject, Customer $customer): bool
    {
        return $customer === $subject->getCustomer();
    }

    protected function canCreate($subject, Customer $customer): bool
    {
        return true;
    }

    protected function canRead(object $subject, Customer $customer): bool
    {
        return $this->customerCanModifyEntity($subject, $customer);
    }

    protected function canUpdate(object $subject, Customer $customer): bool
    {
        return $this->customerCanModifyEntity($subject, $customer);
    }

    protected function canDelete(object $subject, Customer $customer): bool
    {
        return $this->customerCanModifyEntity($subject, $customer);
    }

    protected function canFind($subject, Customer $customer): bool
    {
        return true;
    }
}
