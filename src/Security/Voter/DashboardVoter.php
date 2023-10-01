<?php

namespace App\Security\Voter;

use App\Entity\{
    GB,
    User,
};
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DashboardVoter extends Voter
{

    const DELETE = 'delete';
    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * 
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::DELETE, self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof GB) {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $entry = $subject;

        return match ($attribute) {
            self::DELETE => $entry->canDelete($entry, $user),
            self::VIEW => $entry->canView($entry, $user),
            self::EDIT => $entry->canEdit($entry, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    /**
     * 
     * @param GB $entry
     * @param User $user
     * @return bool
     */
    private function canView(GB $entry, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($entry, $user)) {
            return true;
        }

        return !$entry->isPrivate();
    }

    /**
     * 
     * @param GB $entry
     * @param User $user
     * @return bool
     */
    private function canEdit(GB $entry, User $user): bool
    {
        return $user === $entry->getOwner();
    }

    /**
     * 
     * @param GB $entry
     * @param User $user
     * @return bool
     */
    private function canDelete(GB $entry, User $user): bool
    {
        return $user === $entry->getOwner();
    }
}
