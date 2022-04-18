<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\EventListener\ContaoHooks;

use Contao\BackendUser;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\EventReleaseLevelPolicyModel;
use Contao\Input;
use Contao\System;
use Knp\Menu\MenuItem;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Symfony\Component\Security\Core\Security;

/**
 * @Hook(SacEvtOnGenerateEventDashboardListener::TYPE, priority=50)
 */
class SacEvtOnGenerateEventDashboardListener
{
    public const TYPE = 'sacEvtOnGenerateEventDashboard';

    private ContaoFramework $framework;
    private Security $security;

    public function __construct(ContaoFramework $framework, Security $security)
    {
        $this->framework = $framework;
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
        if (!EventReleaseLevelPolicyModel::hasWritePermission($user->id, $objEvent->id) && (int) $objEvent->registrationGoesTo !== (int) $user->id) {
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
