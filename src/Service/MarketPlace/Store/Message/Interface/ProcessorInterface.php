<?php

namespace App\Service\MarketPlace\Store\Message\Interface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface ProcessorInterface
{
    /**
     * @param array $payload
     * @param UserInterface|null $user
     * @param array|null $exclude
     * @return array
     */
    public function process(array $payload, ?UserInterface $user, ?array $exclude): array;

    /**
     * @param UserInterface|null $user
     * @return JsonResponse
     */
    public function obtainAndResponse(?UserInterface $user): JsonResponse;
}