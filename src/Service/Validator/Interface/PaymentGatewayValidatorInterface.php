<?php declare(strict_types=1);

namespace Inno\Service\Validator\Interface;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface PaymentGatewayValidatorInterface
{
    /**
     * @param mixed $payload
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $payload, ValidatorInterface $validator): ConstraintViolationListInterface;
}