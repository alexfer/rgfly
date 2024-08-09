<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SocketController extends AbstractController
{
    #[Route('/msg', name: 'app_socket')]
    public function index(Request $request): Response
    {
        return new Response('Rgfl Connected to socket!');
    }
}