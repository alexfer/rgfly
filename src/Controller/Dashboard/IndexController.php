<?php

namespace App\Controller\Dashboard;

use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomerOrders;
use App\Entity\MarketPlace\StoreMessage;
use App\Entity\MarketPlace\StoreOrders;
use App\Entity\MarketPlace\StoreProduct;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\{Countries, Locale,};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/dashboard')]
class IndexController extends AbstractController
{

    /**
     * @var int
     */
    private static int $offset = 0;

    /**
     * @var int
     */
    private static int $limit = 10;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/{slug?}', name: 'app_dashboard')]
    public function summaryIndex(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $slug = $request->get('slug');
        $criteria = ['owner' => $user];
        $adminStore = null;
        $ordersIds = [];

        if ($slug) {
            $criteria['slug'] = $slug;
        }

        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            if ($slug) {
                $adminStore = $em->getRepository(Store::class)->findOneBy(['slug' => $slug]);
            }
            $stores = $em->getRepository(Store::class)->findAll();

        } else {
            $stores = $em->getRepository(Store::class)->findBy($criteria);
        }

        $store = $adminStore ?: reset($stores);

        $products = $em->getRepository(StoreProduct::class)->findBy(['store' => $store], ['updated_at' => 'DESC'], self::$limit, self::$offset);
        $orders = $em->getRepository(StoreOrders::class)->findBy(['store' => $store], ['id' => 'DESC'], self::$limit, self::$offset);
        $messages = $em->getRepository(StoreMessage::class)->findBy(['store' => $store], ['created_at' => 'DESC'], self::$limit, self::$offset);

        $ids = array_map(function ($order) {
            return $order->getId();
        }, $orders);

        $customers = $em->getRepository(StoreCustomerOrders::class)->customers($ids, self::$offset, self::$limit);

        return $this->render('dashboard/content/index.html.twig', [
            'stores' => $stores,
            'store' => $store,
            'products' => $products,
            'orders' => $orders,
            'messages' => $messages,
            'countries' => Countries::getNames(Locale::getDefault()),
            'customers' => $customers['result'],
        ]);
    }


}
