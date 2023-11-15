<?php

namespace App\Service\Interface;

use Symfony\Contracts\Translation\TranslatorInterface;

interface ImageValidatorInterface
{
    public function validate($file, TranslatorInterface $translator);
}