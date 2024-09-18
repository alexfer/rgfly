<?php

namespace App\Service\Validator;

use App\Service\Validator\Interface\CarrierValidatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CarrierValidator implements CarrierValidatorInterface
{

    /**
     * @param mixed $payload
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $payload, ValidatorInterface $validator): ConstraintViolationListInterface
    {
        $constraints = [
            'file' => [
                new File([
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    'maxSize' => 1024 * 1024
                ])
            ],
            'name' => [
                new NotBlank([
                    'message' => 'form.name.not_blank'
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'form.name.too_long',
                ]),
            ],
            'description' => [
                new NotBlank([
                    'message' => 'form.description.not_blank'
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'form.description.too_long',
                ]),
            ],
            'linkUrl' => [
                new Regex(
                    '/(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i',
                    'form.url.not_valid',
                ),
            ]
        ];

        return $validator->validate($payload, new Collection($constraints));
    }
}