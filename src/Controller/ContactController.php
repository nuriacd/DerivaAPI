<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/contact')]
class ContactController extends AbstractController {

    #[Route('/', name: 'app_contact_email', methods: ['POST'])]
    public function sendContactEmail(Request $request, MailerInterface $mailer)
    {
       
        $data = json_decode($request->getContent(), true);
        $name = $data["name"];
        $email = $data["email"];
        $cv = $data["cv"];
        $text = "Name: $name\nEmail: $email";
        $issue = $data["issue"];
        
        $message = (new Email())
            ->from('contacto@deriva.x10.mx')
            ->to('contacto@deriva.x10.mx')
            ->replyTo($email)
            ->text($text)
            ->subject($issue)
            ->attach($cv, 'cv.pdf', 'application/pdf');

        try {
            $mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response("true", Response::HTTP_OK);
    }
    
}