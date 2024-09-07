<?php declare(strict_types=1);

namespace App\Service;

use App\Service\Interface\OperationFileValidatorInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OperationOperationFileValidator implements OperationFileValidatorInterface
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
            'mimeTypes' => [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/xml',
                'application/json'
            ],
            'mimeTypesMessage' => $translator->trans('form.picture.not_valid_type', [], 'validators'),
        ]);

        $validator = Validation::createValidator();
        return $validator->validate($file, $imageConstraints);

    }
}