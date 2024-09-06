<?php

declare(strict_types=1);

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\Enum\EnumStoreOrderStatus;
use App\Entity\MarketPlace\StoreInvoice;
use App\Form\Type\MarketPlace\CustomerType;
use App\Form\Type\User\LoginType;
use App\Service\MarketPlace\Store\Checkout\Interface\ProcessorInterface as Checkout;
use App\Service\MarketPlace\Store\Coupon\Interface\ProcessorInterface as Coupon;
use App\Storage\MarketPlace\FrontSessionHandler;
use App\Service\MarketPlace\Store\Customer\Interface\{ProcessorInterface as Customer, UserManagerInterface};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/checkout')]
class CheckoutController extends AbstractController
{

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param UserManagerInterface $userManager
     * @param Checkout $checkout
     * @param Customer $customerManager
     * @param Coupon $coupon
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/confirm/{order}/{tab}', name: 'app_market_place_order_checkout', methods: ['GET', 'POST'])]
    public function checkout(
        Request              $request,
        TranslatorInterface  $translator,
        UserManagerInterface $userManager,
        Checkout             $checkout,
        Customer             $customerManager,
        Coupon               $coupon,
    ): Response
    {
        $session = $request->getSession();
        $hasUsed = false;
        $user = $this->getUser();

        $customer = $userManager->get($user);
        $form = $this->createForm(CustomerType::class, $customer);
        $order = $checkout->findOrder(EnumStoreOrderStatus::Processing->value, $customer);
        $process = $coupon->process($order->getStore());
        $tax = $order->getStore()->getTax();
        $cc = $order->getStore()->getCc();

        if ($process) {
            $hasUsed = $coupon->getCouponUsage($order->getId(), $user);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $securityContext = $this->container->get('security.authorization_checker');
            $isGranted = $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');

            if ($userManager->exists($form->get('email')->getData()) && !$isGranted) {
                $this->addFlash('danger', $translator->trans('email.unique', [], 'validators'));
                return $this->redirectToRoute('app_market_place_order_checkout', ['order' => $request->get('order'), 'tab' => $request->get('tab')]);
            }

            if (!$customer->getId()) {

                $password = substr(base_convert(sha1(uniqid((string)mt_rand())), 16, 36), 0, 8);
                $session->set('_temp_password', $password);

                $customerManager->process($customer, $form->getData(), $order);
                $user = $customerManager->addUser($password);
                $customerManager->bind($form)->addCustomer($user);
            } else {
                $customerManager->bind($form)->updateCustomer($customer, $form->getData());
            }

            $checkout->addInvoice(new StoreInvoice(), floatval($tax));
            $checkout->updateOrder(EnumStoreOrderStatus::Confirmed->value, $customer);

            return $this->redirectToRoute('app_market_place_order_success');
        }

        $sum = $checkout->sum();
        $discount = null;

        if ($process) {
            $discount = $coupon->discount($order->getStore());
        }

        return $this->render('market_place/checkout/index.html.twig', [
            'order' => $order,
            'itemSubtotal' => array_sum($sum['itemSubtotal']),
            'tax' => $tax,
            'creditCards' => $cc,
            'total' => array_sum($sum['itemSubtotal']),
            'form' => $form,
            'hasUsed' => $hasUsed,
            'discount' => $discount,
            'coupon' => $process,
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/order-success/login', name: 'app_market_place_order_success', methods: ['GET', 'POST'])]
    public function checkoutSuccess(
        Request             $request,
        AuthenticationUtils $authenticationUtils,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_cabinet');
        }

        $default = [
            'email' => $authenticationUtils->getLastUsername(),
        ];

        $form = $this->createForm(LoginType::class, $default);
        $form->handleRequest($request);

        $session = $request->getSession();
        $temp_password = $session->get('_temp_password');
        $session->remove('_temp_password');

        $error = $request->getSession()->get(SecurityRequestAttributes::AUTHENTICATION_ERROR);
        $request->getSession()->clear();

        return $this->render('market_place/checkout/order_success.html.twig', [
            'error' => $error,
            'cookie_name' => FrontSessionHandler::NAME,
            'temp_password' => $temp_password,
            'last_username' => $default['email'],
            'form' => $form->createView(),
        ]);
    }
}