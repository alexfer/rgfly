<?php

namespace App\Controller\Dashboard;

use App\Entity\Entry;
use App\Entity\MarketPlace\Store;
use App\Entity\MarketPlace\StoreCustomer;
use App\Entity\MarketPlace\StoreCustomerOrders;
use App\Entity\MarketPlace\StoreMessage;
use App\Entity\MarketPlace\StoreOrders;
use App\Entity\MarketPlace\StoreProduct;
use App\Entity\User;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @throws Exception
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
        $criteriaEntries = ['type' => Entry::TYPE['Blog'], 'user' => $user];

        if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
            $criteriaEntries = ['type' => Entry::TYPE['Blog']];
        }

        $blogs = $em->getRepository(Entry::class)->findBy($criteriaEntries, ['id' => 'DESC'], self::$limit, self::$offset);

        $products = $em->getRepository(StoreProduct::class)->findBy(['store' => $store], ['updated_at' => 'DESC'], self::$limit, self::$offset);
        $orders = $em->getRepository(StoreOrders::class)->findBy(['store' => $store], ['id' => 'DESC'], self::$limit, self::$offset);
        $messages = $em->getRepository(StoreMessage::class)->fetchAll($store, 'low', self::$offset, self::$limit);

        $ids = array_map(function ($order) {
            return $order->getId();
        }, $orders);

        $customers = $em->getRepository(StoreCustomerOrders::class)->customers($ids, self::$offset, self::$limit);

        return $this->render('dashboard/content/index.html.twig', [
            'stores' => $stores,
            'store' => $store,
            'products' => $products,
            'orders' => $orders,
            'messages' => $messages['data'],
            'countries' => Countries::getNames(Locale::getDefault()),
            'customers' => $customers['result'],
            'blogs' => $blogs,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/customer/{id}', name: 'app_dashboard_customer_xhr')]
    public function customerXhr(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        // TODO: check grant access
        $store = $em->getRepository(Store::class)->findOneBy(['owner' => $user]);

        if (!$store) {
            return $this->json(['message' => 'Permission denied'], Response::HTTP_FORBIDDEN);
        }

        $customer = $em->getRepository(StoreCustomer::class)->get($request->get('id'));

        if (!$customer) {
            return $this->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['customer' => $customer], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/lock/{target}', name: 'app_dashboard_lock_xhr', methods: ['POST'])]
    public function lock(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $target = $request->get('target');
        $payload = $request->getPayload()->all();
        $entry = null;

        if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return $this->json(['message' => 'Permission denied'], Response::HTTP_FORBIDDEN);
        }

        if($target == 'entry') {
            $entry = $em->getRepository(Entry::class)->find($payload['id']);
            $entry->setLockedTo(new \DateTime($payload['date']));
            $em->persist($entry);
            $em->flush();
            $entry = $entry->getId();
        }

        return $this->json(['locked' => true, 'entry' => $entry], Response::HTTP_OK);
    }
}
