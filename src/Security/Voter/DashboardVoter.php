<?php

namespace App\Security\Voter;

use App\Entity\{Entry, User,};
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardVoter extends Voter
{

    const DELETE = 'delete';
    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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

        if (!$subject instanceof Entry) {
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
            self::DELETE => $this->canDelete($entry, $user),
            self::VIEW => $this->canView($entry, $user),
            self::EDIT => $this->canEdit($entry, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    /**
     *
     * @param Entry $object
     * @param User $user
     * @return bool
     */
    private function canView(Entry $object, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($object, $user)) {
            return true;
        }

        return !$object->isPrivate();
    }

    /**
     *
     * @param Entry $object
     * @param User $user
     * @return bool
     */
    private function canEdit(Entry $object, User $user): bool
    {
        return $user === $object->getUser();
    }

    /**
     *
     * @param Entry $object
     * @param User $user
     * @return bool
     */
    private function canDelete(Entry $object, User $user): bool
    {
        return $user === $object->getUser();
    }
}
