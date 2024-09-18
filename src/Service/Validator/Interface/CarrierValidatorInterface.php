<?php declare(strict_types=1);

namespace App\Service\Validator\Interface;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

interface CarrierValidatorInterface
{
    /**
     * @param mixed $payload
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $payload, ValidatorInterface $validator): ConstraintViolationListInterface;
}