<?php

namespace App\Service;

use App\Service\Interface\ImageValidatorInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImageValidator implements ImageValidatorInterface
{

    /**
     * @param $file
     * @param TranslatorInterface $translator
     * @return ConstraintViolationListInterface
     */
    public function validate($file, TranslatorInterface $translator): ConstraintViolationListInterface
    {
        $imageConstraints = new Image([
            // Change if needed own size, for instance it can be '2M'
            'maxSize' => ini_get('post_max_size'),
            'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
            'mimeTypesMessage' => $translator->trans('form.picture.not_valid_type', [], 'validators'),
        ]);

        $validator = Validation::createValidator();
        return $validator->validate($file, $imageConstraints);

    }
}