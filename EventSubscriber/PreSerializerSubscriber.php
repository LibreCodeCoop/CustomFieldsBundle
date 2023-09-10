<?php

namespace KimaiPlugin\CustomFieldsBundle\EventSubscriber;

use App\Entity\User;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class PreSerializerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'interface' => User::class],
        ];
    }

    public function onPreSerialize(PreSerializeEvent $event): void
    {
        /** @var User */
        $user = $event->getObject();
        $user->setPreferenceValue('email', $user->getEmail());
    }
}