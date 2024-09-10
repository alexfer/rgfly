<?php declare(strict_types=1);

namespace App\Service\MarketPlace\Store\Message;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreMessage;
use App\Entity\MarketPlace\StoreOrders;
use App\Entity\MarketPlace\StoreProduct;
use App\Entity\UserDetails;
use App\Message\DeleteMessage;
use App\Message\MessageNotification;
use App\Service\MarketPlace\Store\Message\Interface\MessageServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageService implements MessageServiceInterface
{

    /**
     * @var array
     */
    private array $payload;

    /**
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param RouterInterface $router
     * @param MessageBusInterface $bus
     */
    public function __construct(
        private readonly TranslatorInterface       $translator,
        private readonly ValidatorInterface        $validator,
        private readonly EntityManagerInterface    $em,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly RouterInterface           $router,
        private readonly MessageBusInterface       $bus,
    )
    {

    }

    /**
     * @param array $payload
     * @param UserInterface|null $user
     * @param array|null $exclude
     * @param bool $validate
     * @return array|null
     */
    public function process(array $payload, ?UserInterface $user, ?array $exclude, bool $validate = true): ?array
    {
        $this->payload = $payload;
        if ($validate) {
            return $this->validate($exclude, $user);
        }
        return null;
    }

    /**
     * @return StoreMessage
     */
    private function message(): StoreMessage
    {
        return new StoreMessage();
    }

    /**
     * @param UserInterface|null $user
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function obtainAndResponse(?UserInterface $user): JsonResponse
    {
        $customer = $this->customer($user);
        $message = $this->message();
        $message->setMessage($this->payload['message']);
        $message->setStore($this->store());
        $message->setCustomer($customer);
        $message->setProduct($this->product());
        $message->setOrders($this->order());

        $this->em->persist($message);
        $this->em->flush();

        $url = $this->router->generate('app_dashboard_market_place_message_conversation', [
            'store' => $message->getStore()->getId(),
            'id' => $message->getId(),
        ]);

        $request = [
            'id' => $message->getId(),
            'message' => $message->getMessage(),
            'createdAt' => $message->getCreatedAt(),
            'identity' => $message->getIdentity(),
            'from' => sprintf("%s %s", $customer->getFirstName(), $customer->getLastName()),
            'recipient' => $message->getStore()->getOwner()->getEmail(),
            'priority' => $message->getPriority(),
            'url' => $url,
        ];

        $notify = json_encode($request);
        $this->bus->dispatch(new MessageNotification($notify));

        return new JsonResponse([
            'success' => true,
            'message' => $this->translator->trans('store.message.success'),
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
        $repository = $this->em->getRepository(StoreCustomer::class);
        $customer = $repository->findOneBy(['member' => $user]);

        if (!$customer) {
            return $repository->find($this->payload['customer']);
        }

        return $customer;
    }

    /**
     * @param array|null $exclude
     * @param $user
     * @return array
     */
    private function validate(?array $exclude, $user = null): array
    {
        if (!in_array('ROLE_CUSTOMER', $user->getRoles())) {
            return [
                'success' => false,
                'error' => $this->translator->trans('store.message.unauthorized'),
                'code' => Response::HTTP_UNAUTHORIZED,
            ];
        }

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

    /**
     * @param UserInterface|null $user
     * @param bool $customer
     * @return array
     * @throws ExceptionInterface
     */
    public function answer(?UserInterface $user, bool $customer = false): array
    {
        $message = $this->em->getRepository(StoreMessage::class)->find($this->payload['id']);
        $previous = $this->em->getRepository(StoreMessage::class)->find($this->payload['last']);

        $id = $this->payload['last'];
        $recipientEmail = $customer ? $this->customer($user)->getEmail() : $user->getEmail();

        $this->bus->dispatch(new DeleteMessage("{$recipientEmail}:{$id}"));
        $previous->setRead(true);
        $this->em->persist($previous);
        $this->em->flush();

        // new instance
        $answer = $this->message();
        $answer->setCustomer($this->customer($user));

        if (!$customer) {
            $answer->setOwner($user);
        }

        $answer->setStore($this->store())
            ->setParent($message)
            ->setPriority($this->payload['priority'])
            ->setMessage($this->payload['message'])
            ->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($answer);
        $this->em->flush();

        $userDetails = $this->em->getRepository(UserDetails::class)->find($user);

        $recipientName = $customer ?
            sprintf("%s %s", $this->customer($user)->getFirstName(), $this->customer($user)->getLastName()) :
            sprintf("%s %s", $userDetails->getFirstName(), $userDetails->getLastName());

        $url = $this->router->generate('app_dashboard_market_place_message_conversation', [
            'store' => $answer->getStore()->getId(),
            'id' => $answer->getParent()->getId(),
        ]);

        if (!$customer) {
            $url = $this->router->generate('app_cabinet_messages', [
                'id' => $answer->getParent()->getId(),
            ]);
        }

        return [
            'id' => $answer->getId(),
            'store' => $answer->getStore()->getId(),
            'message' => $answer->getMessage(),
            'createdAt' => $answer->getCreatedAt(),
            'parent' => $answer->getParent()->getId(),
            'identity' => $answer->getIdentity(),
            'from' => $recipientName,
            'recipient' => $customer ? $previous->getOwner()->getEmail() : $previous->getCustomer()->getEmail(),
            'customer' => $answer->getCustomer(),
            'priority' => $answer->getPriority(),
            'owner' => $answer->getOwner(),
            'url' => $url,
        ];
    }
}