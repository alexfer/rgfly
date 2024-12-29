<?php declare(strict_types=1);

namespace Essence\Controller;

use Essence\Entity\Contact;
use Essence\Form\Type\ContactType;
use Essence\Service\Contact\ContactTrait;
use Essence\Service\Contact\Interface\HandleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    use ContactTrait;

    /**
     * @param HandleInterface $handle
     * @return Response
     */
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(HandleInterface $handle): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $handler = $handle->serve($form, $contact);

        if ($handler) {
            $handle->notify();
            return $this->redirectToRoute('contact_success');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
