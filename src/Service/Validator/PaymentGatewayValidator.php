<?php declare(strict_types=1);

namespace App\Service\Validator;

use App\Service\Validator\Interface\PaymentGatewayValidatorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentGatewayValidator implements PaymentGatewayValidatorInterface
{

    /**
     * @param mixed $payload
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    public function validate(mixed $payload, ValidatorInterface $validator): ConstraintViolationListInterface
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
            'summary' => [
                new NotBlank([
                    'message' => 'form.description.not_blank'
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'form.description.too_long',
                ]),
            ],
            'handlerText' => [
                new NotBlank([
                    'message' => 'form.handler.not_blank'
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'form.handler.too_long',
                ]),
            ],
            'icon' => [
                new NotBlank([
                    'message' => 'form.icon.not_blank'
                ]),
                new Length([
                    'max' => 50,
                    'maxMessage' => 'form.icon.too_long',
                ])
            ],
            'active' => [],
        ];

        return $validator->validate($payload, new Collection($constraints));
    }
}