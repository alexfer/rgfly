<?php declare(strict_types=1);

namespace App\Service;

use App\Service\Interface\FileValidatorInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FileValidator implements FileValidatorInterface
{

    /**
     * @param mixed $file
     * @param TranslatorInterface $translator
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $file, TranslatorInterface $translator): ConstraintViolationListInterface
    {
        $imageConstraints = new Image([
            // Change if needed own size, for instance it can be '2M'
            'maxSize' => ini_get('post_max_size'),
            'mimeTypes' => ['text/csv', 'text/xml', 'application/json'],
            'mimeTypesMessage' => $translator->trans('form.picture.not_valid_type', [], 'validators'),
        ]);

        $validator = Validation::createValidator();
        return $validator->validate($file, $imageConstraints);

    }
}