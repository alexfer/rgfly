<?php

namespace App\Service\Interface;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

interface ImageValidatorInterface
{
    /**
     * @param $file
     * @param TranslatorInterface $translator
     * @return ConstraintViolationListInterface
     */
    public function validate($file, TranslatorInterface $translator): ConstraintViolationListInterface;
}