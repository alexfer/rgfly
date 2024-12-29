<?php declare(strict_types=1);

namespace Essence\Service\Validator\Interface;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface CustomerRegistrationValidatorInterface
{
    /**
     * @param array $payload
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    public function validate(array $payload, ValidatorInterface $validator): ConstraintViolationListInterface;
}