<?php

namespace App\Service\MarketPlace\Store\Message;


use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreMessage;
use App\Entity\MarketPlace\StoreOrders;
use App\Entity\MarketPlace\StoreProduct;
use App\Service\MarketPlace\Store\Message\Interface\ProcessorInterface;
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
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(
        private readonly TranslatorInterface       $translator,
        private readonly ValidatorInterface        $validator,
        private readonly EntityManagerInterface    $em,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    )
    {

    }

    /**
     * @param array $payload
     * @param array|null $exclude
     * @return array
     */
    public function process(array $payload, ?array $exclude): array
    {
        $this->payload = $payload;
        return $this->validate($exclude);

    }

    /**
     * @param UserInterface|null $user
     * @return JsonResponse
     */
    public function obtainAndResponse(?UserInterface $user): JsonResponse
    {
        $message = new StoreMessage();
        $message->setMessage($this->payload['message']);
        $message->setStore($this->store());
        $message->setCustomer($this->customer($user));
        $message->setProduct($this->product());
        $message->setOrders($this->order());

        $this->em->persist($message);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => $this->translator->trans('market.message.success'),
        ], Response::HTTP_OK);
    }

    /**
     * @return Store
     */
    private function store(): Store
    {
        return $this->em->getRepository(Store::class)->find($this->payload['store']);
    }

    /**
     * @return StoreProduct|null
     */
    private function product(): ?StoreProduct
    {
        if (!isset($this->payload['product'])) {
            return null;
        }
        return $this->em->getRepository(StoreProduct::class)->find($this->payload['product']);
    }

    private function order(): ?StoreOrders
    {
        if (!isset($this->payload['order'])) {
            return null;
        }
        return $this->em->getRepository(StoreOrders::class)->find($this->payload['order']);
    }

    /**
     * @param UserInterface|null $user
     * @return StoreCustomer
     */
    private function customer(?UserInterface $user): StoreCustomer
    {
        return $this->em->getRepository(StoreCustomer::class)->findOneBy(['member' => $user]);
    }

    /**
     * @param array|null $exclude
     * @return array
     */
    private function validate(?array $exclude): array
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
            'store' => [
                new Assert\Required(),
            ],
            'product' => [
                new Assert\Required(),
            ],
            'order' => [
                new Assert\Required(),
            ],
        ];

        if ($exclude) {
            foreach ($exclude as $key => $item) {
                if (!$item) {
                    unset($collection[$key]);
                    unset($this->payload[$key]);
                }
            }
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