<?php

namespace App\Service\MarketPlace\Market\Message\Interface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface ProcessorInterface
{
    /**
     * @param array $payload
     * @param array|null $exclude
     * @return array
     */
    public function process(array $payload, ?array $exclude): array;

    /**
     * @param UserInterface|null $user
     * @return JsonResponse
     */
    public function obtainAndResponse(?UserInterface $user): JsonResponse;
}