<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChatController extends AbstractController{
    #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
    }
    #[Route( '/chat/message', name: 'app_chat_message')]
    public function message(Request $request): Response {
        $message = $request->get('message');
        return $this->json( [
            'answer' => $message
        ] );
    
}
}
