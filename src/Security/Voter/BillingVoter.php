<?php

namespace App\Security\Voter;

use App\Entity\Billing;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BillingVoter extends Voter
{
    public const EXPORT = 'BILLING_EXPORT';
    public const VIEW = 'BILLING_VIEW';

    protected function supports($attribute, $subject)
    {
        return
            in_array($attribute, [self::EXPORT, self::VIEW]) &&
            $subject instanceof Billing;
    }

    /**
     * @param string $attribute
     * @param Billing $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EXPORT:
            case self::VIEW:
                return $user->isAdmin() || $subject->getUser() === $user;
        }

        return false;
    }
}
