<?php

namespace App\Security\Voter;

use App\Entity\MarketPlace\StoreProduct;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{

    const DELETE = 'delete';
    const VIEW = 'view';
    const EDIT = 'edit';


    /**
     * @param Security $security
     */
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::DELETE, self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof StoreProduct) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
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
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    /**
     * @param StoreProduct $product
     * @param User $user
     * @return bool
     */
    private function canView(StoreProduct $product, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($product, $user)) {
            return true;
        }

        return !$product->getStore()->isPrivate();
    }

    /**
     * @param StoreProduct $product
     * @param User $user
     * @return bool
     */
    private function canEdit(StoreProduct $product, User $user): bool
    {
        return $user === $product->getStore()->getOwner();
    }

    /**
     * @param StoreProduct $product
     * @param User $user
     * @return bool
     */
    private function canDelete(StoreProduct $product, User $user): bool
    {
        return $user === $product->getStore()->getOwner();
    }
}