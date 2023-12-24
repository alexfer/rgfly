<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\MarketPlace\Market;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class MarketVoter extends Voter
{

    const string DELETE = 'delete';
    const string VIEW = 'view';
    const string EDIT = 'edit';

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

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::DELETE, self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Market) {
            return false;
        }

        return true;
    }

    #[\Override]
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

        $entity = $subject;

        return match ($attribute) {
            self::DELETE => $this->canDelete($entity, $user),
            self::VIEW => $this->canView($entity, $user),
            self::EDIT => $this->canEdit($entity, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    /**
     * @param Market $market
     * @param User $user
     * @return bool
     */
    private function canView(Market $market, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($market, $user)) {
            return true;
        }

        return !$market->isPrivate();
    }

    /**
     * @param Market $market
     * @param User $user
     * @return bool
     */
    private function canEdit(Market $market, User $user): bool
    {
        return $user === $market->getOwner();
    }

    /**
     * @param Market $market
     * @param User $user
     * @return bool
     */
    private function canDelete(Market $market, User $user): bool
    {
        return $user === $market->getOwner();
    }
}