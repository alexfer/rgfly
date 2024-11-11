<?php

namespace App\Service\MarketPlace\Dashboard\Configuration;

use App\Entity\MarketPlace\StoreCarrier;
use App\Entity\MarketPlace\StorePaymentGateway;
use App\Entity\MarketPlace\StorePaymentGatewayStore;
use App\Service\MarketPlace\Dashboard\Configuration\Interface\ConfigurationServiceInterface;

class ConfigurationService extends HandleService implements ConfigurationServiceInterface
{

    /**
     * @var string
     */
    private string $target;

    /**
     * @var int
     */
    private mixed $id;


    /**
     * @param string|null $target
     * @param mixed|null $id
     * @return $this
     */
    public function take(?string $target = null, mixed $id = null): self
    {
        $this->request = $this->requestStack->getCurrentRequest();
        $this->target = $target;
        $this->id = $id;
        return $this;
    }

    /**
     * @param array|null $criteria
     * @param array|null $order
     * @return array
     */
    public function collection(?array $criteria = [], ?array $order = ['id' => 'DESC']): array
    {
        return [
            'carriers' => $this->manager->getRepository(StoreCarrier::class)->findBy($criteria, $order),
            'payment_gateways' => $this->manager->getRepository(StorePaymentGateway::class)->findBy($criteria, $order),
        ];
    }

    /**
     * @return bool
     */
    public function remove(): bool
    {
        if ($this->target == 'carrier') {
            $target = $this->manager->getRepository(StoreCarrier::class)->find((int)$this->id);
            $this->manager->remove($target);
        }

        if ($this->target == 'payment_gateway') {
            $target = $this->manager->getRepository(StorePaymentGateway::class)->find($this->id);

            if ($this->manager->getRepository(StorePaymentGatewayStore::class)->findOneBy(['gateway' => $target])) {
                return false;
            }
            $this->manager->remove($target);
        }

        $this->manager->flush();

        return true;
    }

    public function createTarget()
    {
        if ($this->target == 'carrier') {
            $this->createCarrier();
        }

        if ($this->target == 'payment_gateway') {
            $this->createPaymentGateway($this->target);
        }
    }
}