<?php declare(strict_types=1);

namespace Inno\Service\Validator;

use Inno\Service\Validator\Interface\CarrierValidatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
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
     * @param bool $change
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $payload, ValidatorInterface $validator, bool $change = false): ConstraintViolationListInterface
    {
        $constraints = [
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
            'shippingAmount' => [
                new Regex(
                    '/\d+(\.\d+)?/',
                    'form.money.not_valid'),
            ],
            'linkUrl' => [
                new Regex(
                    '/(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i',
                    'form.url.not_valid',
                ),
            ],
            'enabled' => [],
        ];

        if ($change) {
            unset($constraints['file'], $constraints['name']);
        }

        return $validator->validate($payload, new Collection($constraints));
    }
}