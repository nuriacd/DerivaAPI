<?php
namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    /**
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        // Get the user
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        // Get the current payload
        $payload = $event->getData();

        // Add custom data
        $payload['id'] = $user->getId();

        // Set the new payload
        $event->setData($payload);
    }
}
