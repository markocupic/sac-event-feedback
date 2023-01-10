<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\EventListener\ContaoHooks;

use Contao\BackendUser;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Input;
use Contao\System;
use Knp\Menu\MenuItem;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Markocupic\SacEventToolBundle\Security\Voter\CalendarEventsVoter;
use Symfony\Component\Security\Core\Security;

#[AsHook(GenerateEventDashboardListener::HOOK, priority: 100)]
class GenerateEventDashboardListener
{
    public const HOOK = 'generateEventDashboard';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(MenuItem $menu, CalendarEventsModel $objEvent): void
    {
        if (null === EventFeedbackModel::findByPid($objEvent->id)) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof BackendUser) {
            return;
        }

        // Apply same permission rules like "teilnehmerliste"
        if (!$this->security->isGranted(CalendarEventsVoter::CAN_WRITE_EVENT, $objEvent->id) && (int) $objEvent->registrationGoesTo !== (int) $user->id) {
            return;
        }

        $container = System::getContainer();
        $requestToken = $container
            ->get('contao.csrf.token_manager')
            ->getToken($container->getParameter('contao.csrf_token_name'))
            ->getValue()
        ;

        // Get the refererId
        $refererId = System::getContainer()
            ->get('request_stack')
            ->getCurrentRequest()
            ->get('_contao_referer_id')
        ;

        // Get the backend module name
        $module = Input::get('do');

        // "Download event feedbacks" button
        $eventListHref = sprintf('contao/main.php?do=%s&key=showEventFeedbacks&id=%s&rt=%s&ref=%s', $module, $objEvent->id, $requestToken, $refererId);
        $menu->addChild('Event Auswertungen', ['uri' => $eventListHref])
            ->setLinkAttribute('role', 'button')
            ->setLinkAttribute('class', 'tl_submit')
            ->setLinkAttribute('target', '_blank')
            //->setLinkAttribute('accesskey', 'm')
            ->setLinkAttribute('title', 'Event Ausertungen herunterladen')
        ;
    }
}
