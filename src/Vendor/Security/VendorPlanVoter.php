<?php

namespace App\Vendor\Security;

use App\Vendor\Entity\VendorPlan;
use App\Vendor\Entity\Vendor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VendorPlanVoter extends Voter
{
    public const DELETE = 'delete';
    public const EDIT = 'edit';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof VendorPlan && \in_array($attribute, [self::EDIT, self::DELETE], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $vendorPlan, TokenInterface $token): bool
    {
        $vendor = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$vendor instanceof Vendor) {
            return false;
        }

        // the logic of this voter is pretty simple: if the logged user is the
        // author of the given blog post, grant permission; otherwise, deny it.
        // (the supports() method guarantees that $post is a Post object)
        return $vendor === $vendorPlan->getVendor();
    }
}
