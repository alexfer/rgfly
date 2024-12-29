<?php declare(strict_types=1);

namespace Essence\Service\MarketPlace;

use Essence\Entity\MarketPlace\Store;
use Essence\Service\MarketPlace\Dashboard\Store\StoreStore;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait StoreTrait
{
    public final const int LIMIT = 25;

    public int $offset = 0;

    /**
     * @var PaginatorInterface
     */
    protected PaginatorInterface $paginator;

    /**
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        return $this->paginator = $paginator;
    }

    /**
     * @param StoreStore $serve
     * @param UserInterface $user
     * @return Store
     */
    protected function store(StoreStore $serve, UserInterface $user): Store
    {
        return $serve->supports($user);
    }

}