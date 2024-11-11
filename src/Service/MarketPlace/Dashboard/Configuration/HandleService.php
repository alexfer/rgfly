<?php declare(strict_types=1);

namespace App\Service\MarketPlace\Dashboard\Configuration;

use App\Entity\MarketPlace\StorePaymentGateway;
use App\Service\Validator\Interface\CarrierValidatorInterface;
use App\Service\Validator\Interface\PaymentGatewayValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HandleService
{
    /**
     * @var Request|null
     */
    protected ?Request $request;

    /**
     * @param EntityManagerInterface $manager
     * @param RequestStack $requestStack
     */
    public function __construct(
        protected readonly EntityManagerInterface         $manager,
        protected readonly RequestStack                   $requestStack,
        private readonly ContainerInterface               $container,
        private readonly CarrierValidatorInterface        $carrierValidator,
        private readonly PaymentGatewayValidatorInterface $paymentGatewayValidator,
        private readonly ValidatorInterface               $validator,
        private readonly SluggerInterface                 $slugger,
    )
    {

    }

    protected function createCarrier()
    {

    }

    /**
     * @param string $target
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function createPaymentGateway(string $target)
    {
        $payload = $this->request->getPayload()->all();
        $inputs = $payload[$target];

        try {
            $this->isCsrfTokenValid($target, $inputs['_token']);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        unset($inputs['save'], $inputs['_token']);

        $errors = $this->paymentGatewayValidator->validate($inputs, $this->validator);

        if (count($errors) > 0) {
            return $errors;
        }

        $target = new StorePaymentGateway();

        $target
            ->setName($inputs['name'])
            ->setSummary($inputs['summary'])
            ->setSlug($this->slugger->slug($inputs['name'])->lower()->toString())
            ->setHandlerText($inputs['handlerText'])
            ->setIcon($inputs['icon'])
            ->setActive($inputs['active']);

        $this->manager->persist($target);
        $this->manager->flush();
    }

    protected function updateCarrier()
    {

    }

    protected function updatePaymentGateway()
    {

    }

    /**
     * @param string $id
     * @param string|null $token
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function isCsrfTokenValid(string $id, #[\SensitiveParameter] ?string $token): bool
    {
        if (!$this->container->has('security.csrf.token_manager')) {
            throw new \LogicException('CSRF protection is not enabled in your application. Enable it with the "csrf_protection" key in "config/packages/framework.yaml".');
        }

        return $this->container->get('security.csrf.token_manager')->isTokenValid(new CsrfToken($id, $token));
    }
}