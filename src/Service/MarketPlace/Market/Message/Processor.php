<?php

namespace App\Service\MarketPlace\Market\Message;


use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCustomer;
use App\Entity\MarketPlace\MarketMessage;
use App\Entity\MarketPlace\MarketProduct;
use App\Service\MarketPlace\Market\Message\Interface\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Processor implements ProcessorInterface
{

    private array $payload;

    /**
     * @param UserInterface|null $user
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(
        private readonly ?UserInterface            $user,
        private readonly TranslatorInterface       $translator,
        private readonly ValidatorInterface        $validator,
        private readonly EntityManagerInterface    $em,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    )
    {

    }

    /**
     * @param array $payload
     * @param string|null $exclude
     * @return array
     */
    public function process(array $payload, ?string $exclude = null): array
    {
        $this->payload = $payload;
        return $this->validate($exclude);

    }

    /**
     * @return JsonResponse
     */
    public function obtainAndResponse(?UserInterface $user): JsonResponse
    {
        $message = new MarketMessage();
        $message->setMessage($this->payload['message']);
        $message->setMarket($this->market());
        $message->setCustomer($this->customer($user));
        $message->setProduct($this->product());

        $this->em->persist($message);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => $this->translator->trans('market.message.success'),
        ], Response::HTTP_OK);
    }

    /**
     * @return Market
     */
    private function market(): Market
    {
        return $this->em->getRepository(Market::class)->find($this->payload['market']);
    }

    /**
     * @return MarketProduct|null
     */
    private function product(): ?MarketProduct
    {
        if(!isset($this->payload['product'])) {
            return null;
        }
        return $this->em->getRepository(MarketProduct::class)->find($this->payload['product']);
    }

    /**
     * @param UserInterface|null $user
     * @return MarketCustomer
     */
    private function customer(?UserInterface $user): MarketCustomer
    {
        return $this->em->getRepository(MarketCustomer::class)->findOneBy(['member' => $user]);
    }

    /**
     * @param string|null $exclude
     * @return array
     */
    private function validate(?string $exclude = null): array
    {
        $jsonErrors = [];
        $collection = [
            'message' => [
                new Assert\NotBlank(),
                new Assert\Required(),
            ],
            '_token' => [
                new Assert\Required(),
            ],
            'market' => [
                new Assert\Required(),
            ],
            'product' => [
                new Assert\Required(),
            ],
        ];

        if ($exclude) {
            unset($collection[$exclude]);
            unset($this->payload[$exclude]);
        }

        $errors = $this->validator->validate($this->payload, new Assert\Collection($collection));

        if ($errors->count()) {
            foreach ($errors as $error) {
                $jsonErrors = [
                    'success' => false,
                    'error' => $error->getMessage(),
                    'constraint' => $error->getInvalidValue(),
                    'code' => Response::HTTP_BAD_REQUEST,
                ];
            }
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('obtain', $this->payload['_token']))) {
            $jsonErrors = [
                'success' => false,
                'error' => 'Something went wrong with validation token.',
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }

        return $jsonErrors;
    }
}