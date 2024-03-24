<?php

namespace App\Controller\Security;

use App\Entity\{MarketPlace\MarketCustomer, User};
use App\Form\Type\MarketPlace\CustomerRegistrationType;
use App\Form\Type\User\DetailsType;
use App\Repository\MarketPlace\MarketAddressRepository;
use App\Repository\MarketPlace\MarketCustomerRepository;
use App\Repository\UserDetailsRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param UserRepository $userRepository
     * @param MarketCustomerRepository $customerRepository
     * @param MarketAddressRepository $addressRepository
     * @param ParameterBagInterface $params
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    #[Route('/customer', name: 'app_customer_register', methods: ['GET', 'POST'])]
    public function customerRegister(
        Request                     $request,
        UserPasswordHasherInterface $hasher,
        UserRepository              $userRepository,
        MarketCustomerRepository    $customerRepository,
        MarketAddressRepository     $addressRepository,
        ParameterBagInterface       $params,
        TranslatorInterface         $translator,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_index');
        }

        $form = $this->createForm(CustomerRegistrationType::class, new MarketCustomer());
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $this->userData($form, $request, $hasher);

            $user = $userRepository->create($userData);

            if ($user == -1) {
                $error = $translator->trans('email.unique', [], 'validators');
                $form->addError(new FormError($error));
            }

            if (!$error) {
                $customerData = [
                    'first_name' => $form->get('first_name')->getData(),
                    'last_name' => $form->get('last_name')->getData(),
                    'country' => $form->get('country')->getData(),
                    'phone' => $form->get('phone')->getData(),
                    'email' => $form->get('email')->getData(),
                ];

                $customer = $customerRepository->create($user, $customerData);

                $addressData = [
                    'line1' => $form->get('address')->getData(),
                    'country' => $form->get('country')->getData(),
                    'phone' => $form->get('phone')->getData(),
                    'city' => $form->get('city')->getData(),
                ];

                $addressRepository->create($customer, $addressData);

                if ($params->get('auto_login')) {
                    $this->authenticateUser($userRepository->find($user));
                    return $this->redirectToRoute('app_cabinet');
                }
                return $this->redirectToRoute('app_register_success');
            }
        }

        return $this->render('registration/customer_register.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param UserRepository $userRepository
     * @param UserDetailsRepository $detailsRepository
     * @param ParameterBagInterface $params
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $hasher,
        UserRepository              $userRepository,
        UserDetailsRepository       $detailsRepository,
        ParameterBagInterface       $params,
        TranslatorInterface         $translator,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_index');
        }

        $user = new User();

        $form = $this->createForm(DetailsType::class, $user);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $userData = $this->userData($form, $request, $hasher);
            $newUser = $userRepository->create($userData);

            if ($newUser == -1) {
                $error = $translator->trans('email.unique', [], 'validators');
                $form->addError(new FormError($error));
            }

            if (!$error) {
                $date = new \DateTime();
                $detailsData = [
                    'first_name' => $form->get('first_name')->getData(),
                    'last_name' => $form->get('last_name')->getData(),
                    'date_birth' => $date->modify("-16 years")->format('Y-m-d'),
                ];

                $detailsRepository->create($newUser, $detailsData);

                if ($params->get('auto_login')) {
                    $this->authenticateUser($userRepository->find($newUser));
                    return $this->redirectToRoute('app_dashboard');
                }
                return $this->redirectToRoute('app_register_success');
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return array
     */
    private function userData(
        FormInterface               $form,
        Request                     $request,
        UserPasswordHasherInterface $hasher,
    ): array
    {
        return [
            'email' => $form->get('email')->getData(),
            'roles' => [User::ROLE_USER],
            'password' => $hasher->hashPassword(new User(), $form->get('plainPassword')->getData()),
            'ip' => $request->getClientIp(),
        ];
    }

    /**
     * @param User $user
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function authenticateUser(User $user): void
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, $providerKey, $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
    }

    /**
     *
     * @return Response
     */
    #[Route('/register/success', name: 'app_register_success')]
    public function success(): Response
    {
        return $this->render('registration/success.html.twig', []);
    }
}
