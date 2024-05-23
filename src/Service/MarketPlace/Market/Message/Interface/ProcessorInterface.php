<?php

namespace App\Service\MarketPlace\Market\Message\Interface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface ProcessorInterface
{
    /**
     * @param array $payload
     * @param string|null $exclude
     * @return array
     */
    public function process(array $payload, ?string $exclude = null): array;

    /**
     * @param UserInterface|null $user
     * @return JsonResponse
     */
    public function obtainAndResponse(?UserInterface $user): JsonResponse;
}