<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\EventListener\ContaoHooks;

use Contao\BackendUser;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;
use Contao\System;
use Knp\Menu\MenuItem;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;

/**
 * @Hook(SacEvtOnGenerateEventDashboardListener::TYPE, priority=50)
 */
class SacEvtOnGenerateEventDashboardListener
{
    public const TYPE = 'sacEvtOnGenerateEventDashboard';
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * SacEvtOnGenerateEventDashboardListener constructor.
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function __invoke(MenuItem $menu, CalendarEventsModel $objEvent): void
    {
        $feedbacks = EventFeedbackModel::findByPid($objEvent->id);

        if (null === $feedbacks) {
            return;
        }
        
        $container = System::getContainer();
        $requestToken = $container
            ->get('contao.csrf.token_manager')
            ->getToken($container->getParameter('contao.csrf_token_name'))
            ->getValue()
        ;

        $objCalendar = $objEvent->getRelated('pid');

        // Get the refererId
        $refererId = System::getContainer()
            ->get('request_stack')
            ->getCurrentRequest()
            ->get('_contao_referer_id')
        ;

        // Get the backend module name
        $module = Input::get('do');

        // "Download event feedbacks" button
        $eventListHref = sprintf('contao/main.php?do=%s&table=tl_calendar_events&key=getEventFeedbacks&id=%s&rt=%s&ref=%s', $module, $objEvent->id, $requestToken, $refererId);
        $menu->addChild('Event Auswertungen', ['uri' => $eventListHref])
            ->setLinkAttribute('role', 'button')
            ->setLinkAttribute('class', 'tl_submit')
            ->setLinkAttribute('target', '_blank')
            //->setLinkAttribute('accesskey', 'm')
            ->setLinkAttribute('title', 'Event Ausertungen herunterladen')
        ;
    }
}
