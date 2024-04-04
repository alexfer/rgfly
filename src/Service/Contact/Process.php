<?php

namespace App\Service\Contact;

use App\Entity\Contact;
use App\Service\Contact\Interface\HandleInterface;
use App\Service\Interface\EmailNotificationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Process implements HandleInterface
{

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $params
     * @param UrlGeneratorInterface $generator
     * @param EmailNotificationInterface $emailNotification
     * @param Twig $twig
     */
    public function __construct(
        protected RequestStack                      $requestStack,
        private readonly EntityManagerInterface     $em,
        private readonly ParameterBagInterface      $params,
        private readonly UrlGeneratorInterface      $generator,
        private readonly EmailNotificationInterface $emailNotification,
        private readonly Twig                       $twig

    )
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param FormInterface $form
     * @param Contact $contact
     * @return bool
     */
    public function serve(FormInterface $form, Contact $contact): bool
    {
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setSubject(
                $form->get('subject')->getData() ?:
                    $this->params->get('app.notifications.subject')
            );

            $this->em->persist($contact);
            $this->em->flush();
            return true;
        }
        return false;
    }

    /**
     * @param array $args
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function renderView(array $args): string
    {
        return $this->twig->render('mail/default.html.twig', $args);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function notify(): void
    {
        $args = $this->request->request->all();
        $template = $this->renderView([
            'args' => $args['contact'],
            'subject' => $this->params->get('app.notifications.subject'),
            'index' => $this->generator->generate('app_index')
        ]);

        $this->emailNotification->send($args['contact'], $template);
    }
}