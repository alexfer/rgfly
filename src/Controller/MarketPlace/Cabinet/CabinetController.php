<?php

namespace App\Controller\MarketPlace\Cabinet;

use App\Entity\MarketPlace\{StoreCustomer, StoreCustomerOrders, StoreMessage, StoreOrders, StoreWishlist};
use App\Form\Type\MarketPlace\{AddressType, CustomerProfileType};
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/cabinet')]
class CabinetController extends AbstractController
{

    /**
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return StoreCustomer
     */
    private function customer(
        UserInterface          $user,
        EntityManagerInterface $em,
    ): StoreCustomer
    {
        return $em->getRepository(StoreCustomer::class)->findOneBy([
            'member' => $user,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('', name: 'app_cabinet', methods: ['GET'])]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 25);
        $customer = $this->customer($user, $em);
        $result = $em->getRepository(StoreCustomerOrders::class)->getCustomerOrders($customer->getId(), $offset, $limit);
        //dd($result['orders']);

//        $summary = $fee = $products = [];
//        foreach ($orders as $order) {
//            foreach ($order->getOrders()->getStoreOrdersProducts() as $item) {
//                $products[$order->getId()][] = $item->getCost() - ((($item->getCost() * $item->getQuantity()) * $item->getDiscount()) - $item->getDiscount()) / 100;
//                $fee[$order->getId()][] = $item->getProduct()->getFee();
//            }
//            $summary[$order->getOrders()->getId()] = [
//                'total' => array_sum($products[$order->getId()]) + array_sum($fee[$order->getId()]),
//            ];
//        }

        return $this->render('market_place/cabinet/orders.html.twig', [
            'customer' => $customer,
            'orders' => $result['orders'],
            'rows' => $result['rows_count'],
            //'summary' => $summary,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('/messages', name: 'app_cabinet_messages', methods: ['GET'])]
    public function messages(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $this->customer($user, $em);
        $messages = $em->getRepository(StoreMessage::class)->fetchByCustomer($customer);

        return $this->render('market_place/cabinet/message/index.html.twig', [
            'customer' => $customer,
            'messages' => $messages['data'],
        ]);
    }

    #[Route('/search-order', name: 'app_cabinet_search_order', methods: ['POST'])]
    public function searchOrders(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $query = $request->getPayload()->get('query');
        $customer = $this->customer($user, $em);
        $order = $em->getRepository(StoreOrders::class)->singleFetch($query, $customer);

        return $this->json([
            'query' => $query,
            'order' => $order,
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/personal-information', name: 'app_cabinet_personal_information', methods: ['GET', 'POST'])]
    public function personalInformation(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $customer = $this->customer($user, $em);
        $form = $this->createForm(CustomerProfileType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $customer->setFirstName($form->get('first_name')->getData())
                ->setLastName($form->get('last_name')->getData())
                ->setEmail($form->get('email')->getData())
                ->setPhone($form->get('phone')->getData())
                ->setCountry($form->get('country')->getData())
                ->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($customer);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.profile.updated')]));
            return $this->redirectToRoute('app_cabinet_personal_information');
        }

        return $this->render('market_place/cabinet/personal_information.html.twig', [
            'customer' => $customer,
            'form' => $form,
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/wishlist', name: 'app_cabinet_wishlist', methods: ['GET'])]
    public function wishlist(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $this->customer($user, $em);

        $wishlist = $em->getRepository(StoreWishlist::class)->findBy([
            'customer' => $customer,
        ], ['created_at' => 'desc']);

        return $this->render('market_place/cabinet/wishlist.html.twig', [
            'customer' => $customer,
            'wishlist' => $wishlist,
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/wishlist/bulk-delete', name: 'app_cabinet_wishlist_bulk_delete', methods: ['POST'])]
    public function wishlistBulkDelete(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $customer = $this->customer($user, $em);
        $parameters = json_decode($request->getContent(), true);
        $items = $parameters['items'];

        foreach ($parameters['items'] as $key => $item) {
            $wishlist = $em->getRepository(StoreWishlist::class)
                ->findOneBy(['customer' => $customer, 'id' => $item]);
            $em->remove($wishlist);
        }
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_NO_CONTENT);

        return $response->send();
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/address', name: 'app_cabinet_address', methods: ['GET', 'POST'])]
    public function address(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $customer = $this->customer($user, $em);
        $address = $customer->getStoreAddress();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setLine1($form->get('line1')->getData())
                ->setLine2($form->get('line2')->getData())
                ->setCity($form->get('city')->getData())
                ->setCountry($form->get('country')->getData())
                ->setRegion($form->get('region')->getData())
                ->setPhone($form->get('phone')->getData())
                ->setPostal($form->get('postal')->getData())
                ->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($address);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.address.updated')]));
            return $this->redirectToRoute('app_cabinet_address');
        }

        return $this->render('market_place/cabinet/address.html.twig', [
            'customer' => $customer,
            'form' => $form,
            'errors' => $form->getErrors(true),
        ]);
    }

}