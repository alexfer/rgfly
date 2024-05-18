<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\MarketInvoice;
use App\Form\Type\MarketPlace\CustomerType;
use App\Form\Type\User\LoginType;
use App\Service\MarketPlace\Currency;
use App\Service\MarketPlace\Market\Checkout\Interface\ProcessorInterface as CheckoutProcessorInterface;
use App\Service\MarketPlace\Market\Customer\Interface\{ProcessorInterface as CustomerProcessorInterface,
    UserManagerInterface,};
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/market-place/checkout')]
class CheckoutController extends AbstractController
{

    /**
     * @param Request $request
     * @param UserInterface|null $user
     * @param TranslatorInterface $translator
     * @param UserManagerInterface $userManager
     * @param CheckoutProcessorInterface $checkoutProcessor
     * @param CustomerProcessorInterface $customerProcessor
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{order}', name: 'app_market_place_order_checkout', methods: ['GET', 'POST'])]
    public function checkout(
        Request                    $request,
        ?UserInterface             $user,
        TranslatorInterface        $translator,
        UserManagerInterface       $userManager,
        CheckoutProcessorInterface $checkoutProcessor,
        CustomerProcessorInterface $customerProcessor,
    ): Response
    {
        $session = $request->getSession();
        $sessionId = $session->getId();
        $hasUsed = false;

        $customer = $userManager->getUserCustomer($user);
        $form = $this->createForm(CustomerType::class, $customer);
        $order = $checkoutProcessor->findOrder($sessionId);
        $coupon = $checkoutProcessor->getCoupon($order->getMarket());

        if ($coupon) {
            $hasUsed = $checkoutProcessor->getCouponUsage($coupon['id'], $order->getId(), $customer);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$order) {
                return $this->redirectToRoute('app_market_place_order_summary');
            }

            if ($coupon) {
                $checkoutProcessor->updateOrderAmount($coupon, $order);
            }

            $securityContext = $this->container->get('security.authorization_checker');
            $isGranted = $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');

            if ($userManager->existsCustomer($form->get('email')->getData()) && !$isGranted) {
                $this->addFlash('danger', $translator->trans('email.unique', [], 'validators'));
                return $this->redirectToRoute('app_market_place_order_checkout', ['order' => $request->get('order')]);
            }

            if (!$customer->getId()) {

                $password = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8);
                $session->set('_temp_password', $password);

                $customerProcessor->process($customer, $form->getData(), $order);
                $user = $customerProcessor->addUser($password);
                $customerProcessor->bind($form)->addCustomer($user);
            } else {
                $customerProcessor->bind($form)->updateCustomer($customer, $form->getData());
            }

            $checkoutProcessor->addInvoice(new MarketInvoice());
            $checkoutProcessor->updateOrder();
            $session->set('quantity', $checkoutProcessor->countOrders());

            return $this->redirectToRoute('app_market_place_order_success');
        }

        $sum = $checkoutProcessor->sum();
        $discount = null;

        if ($coupon) {
            $currency = Currency::currency($order->getMarket()->getCurrency());
            $discount = $coupon['price'] ? number_format($coupon['price'], 2, ',', ' ') . $currency : null;

            if ($coupon['discount']) {
                $discount = sprintf("%d%%", $coupon['discount']);
            }
        }

        return $this->render('market_place/checkout/index.html.twig', [
            'order' => $order,
            'itemSubtotal' => array_sum($sum['itemSubtotal']) + array_sum($sum['fee']),
            'fee' => array_sum($sum['fee']),
            'total' => array_sum($sum['fee']) + array_sum($sum['itemSubtotal']),
            'form' => $form,
            'hasUsed' => $hasUsed,
            'discount' => $discount,
            'coupon' => $coupon,
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
            'temp_password' => $temp_password,
            'last_username' => $default['email'],
            'form' => $form->createView(),
        ]);
    }
}