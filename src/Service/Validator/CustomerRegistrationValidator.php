<?php declare(strict_types=1);

namespace Inno\Service\Validator;

use Inno\Service\Validator\Interface\CustomerRegistrationValidatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerRegistrationValidator implements CustomerRegistrationValidatorInterface
{
    public function validate(mixed $payload, ValidatorInterface $validator): ConstraintViolationListInterface
    {
        $constraints = [
            'first_name' => [
                new NotBlank([
                    'message' => 'form.first_name.not_blank',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'form.first_name.min',
                    'max' => 200,
                    'maxMessage' => 'form.first_name.max',
                ])
            ],
            'last_name' => [
                new NotBlank([
                    'message' => 'form.last_name.not_blank',
                ]),
                new Length([
                    'min' => 2,
                    'minMessage' => 'form.last_name.min',
                    'max' => 200,
                    'maxMessage' => 'form.last_name.max',
                ])
            ],
            'email' => [new NotBlank([
                'message' => 'form.email.not_blank',
            ]), new Email(
                message: 'form.email.not_valid'
            )],
            'phone' => [new Regex([
                'pattern' => "/[+0-9]+$/i",
                'message' => 'form.phone.not_valid',
            ])],
            'city' => [
                new NotBlank([
                    'message' => 'form.city.not_blank',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'form.city.min',
                    'max' => 250,
                    'maxMessage' => 'form.city.max',
                ]),
            ],
            'address' => [
                new NotBlank([
                    'message' => 'form.address.not_blank',
                ]),
                new Length([
                    'min' => 10,
                    'minMessage' => 'form.address.min',
                    'max' => 250,
                    'maxMessage' => 'form.address.max',
                ])
            ],
            'country' => [new NotBlank([
                'message' => 'form.country.not_blank',
            ])],
            'plainPassword' => [new NotBlank([
                'message' => 'form.password.not_blank',
            ])],
            'agreeTerms' => [new IsTrue([
                'message' => 'form.message.agree_terms',
            ])],
            'order' => [new NotBlank([
                'message' => 'form.order.not_blank',
            ])]
        ];

        return $validator->validate($payload, new Collection($constraints));
    }
}