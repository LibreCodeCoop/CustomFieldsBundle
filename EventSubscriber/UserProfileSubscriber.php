<?php

namespace KimaiPlugin\CustomFieldsBundle\EventSubscriber;

use App\Entity\UserPreference;
use App\Event\UserPreferenceDisplayEvent;
use App\Event\UserPreferenceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\EventDispatcher\Event;

class UserProfileSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UserPreferenceDisplayEvent::class => ['loadUserPreferencesDisplayEvent', 200],
            UserPreferenceEvent::class => ['loadUserPreferences', 200]
        ];
    }

    public function loadUserPreferencesDisplayEvent(UserPreferenceDisplayEvent $event)
    {
        $this->addPreferences($event);
    }

    public function loadUserPreferences(UserPreferenceEvent $event): void
    {
        $this->addPreferences($event);
    }

    /**
     * @param UserPreferenceEvent $event
     */
    private function addPreferences(Event $event): void
    {
        $event->addPreference(
            (new UserPreference('tax_number', ''))
                ->setOrder(900)
                ->setSection('extra_fields')
                ->setOptions(['required' => false, 'label' => 'CPF'])
                ->setEnabled(true)
                ->setType(TextType::class)
        );
        $event->addPreference(
            (new UserPreference('dependents', ''))
                ->setOrder(900)
                ->setSection('extra_fields')
                ->setOptions(['required' => false, 'label' => 'Dependentes'])
                ->setEnabled(true)
                ->setType(NumberType::class)
        );
    }
}
