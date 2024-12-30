<?php declare(strict_types=1);

namespace Inno\Service\Validator\Interface;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

interface ImageValidatorInterface
{
    /**
     * @param mixed $file
     * @param TranslatorInterface $translator
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $file, TranslatorInterface $translator): ConstraintViolationListInterface;
}