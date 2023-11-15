<?php

namespace App\Service\Interface;

use Symfony\Contracts\Translation\TranslatorInterface;

interface ImageValidatorInterface
{
    /**
     * @param $file
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function validate($file, TranslatorInterface $translator);
}