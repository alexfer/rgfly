<?php declare(strict_types=1);

namespace Essence\Service\Validator\Interface;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

interface CarrierValidatorInterface
{
    /**
     * @param mixed $payload
     * @param ValidatorInterface $validator
     * @param bool $change
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $payload, ValidatorInterface $validator, bool $change = false): ConstraintViolationListInterface;
}