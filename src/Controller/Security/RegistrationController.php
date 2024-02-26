<?php

namespace App\Controller\Security;

use App\Entity\{MarketPlace\MarketAddress, MarketPlace\MarketCustomer, User, UserDetails, UserSocial};
use App\Form\Type\MarketPlace\CustomerRegistrationType;
use App\Form\Type\User\DetailsType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response,};
use Symfony\Component\Form\FormError;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $params
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/customer', name: 'app_customer_register', methods: ['GET', 'POST'])]
    public function customerRegister(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $em,
        ParameterBagInterface       $params,
        TranslatorInterface         $translator,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_index');
        }

        $customer = new MarketCustomer();
        $user = new User();
        $address = new MarketAddress();

        $form = $this->createForm(CustomerRegistrationType::class, $customer);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $checkEmail = $em->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);

            if ($checkEmail) {
                $error = $translator->trans('email.unique', [], 'validators');
                $form->addError(new FormError($error));
            }

            if(!$error) {
                $user->setEmail($form->get('email')->getData());

                $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

                $user->setIp($request->getClientIp())
                    ->setRoles([User::ROLE_CUSTOMER]);

                $em->persist($user);

                $customer->setMember($user)
                    ->setFirstName($form->get('first_name')->getData())
                    ->setLastName($form->get('last_name')->getData())
                    ->setCountry($form->get('country')->getData())
                    ->setPhone($form->get('phone')->getData())
                    ->setEmail($form->get('email')->getData());

                $em->persist($customer);

                $address->setLine1($form->get('address')->getData())
                    ->setCountry($form->get('country')->getData())
                    ->setPhone($form->get('phone')->getData())
                    ->setCity($form->get('city')->getData());

                $em->persist($address);
                $em->flush();

                if ($params->get('auto_login')) {
                    $this->authenticateUser($user);
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
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $params
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $em,
        ParameterBagInterface       $params,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_index');
        }

        $user = new User();
        $details = new UserDetails();
        $social = new UserSocial();

        $form = $this->createForm(DetailsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setIp($request->getClientIp());

            $em->persist($user);

            $details->setFirstName($form->get('first_name')->getData())
                ->setLastName($form->get('last_name')->getData())
                ->setUser($user);

            $em->persist($details);

            $social->setDetails($details);
            $em->persist($social);

            $em->flush();

            if ($params->get('auto_login')) {
                $this->authenticateUser($user);
                return $this->redirectToRoute('app_dashboard');
            }
            return $this->redirectToRoute('app_register_success');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
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
