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

namespace Markocupic\SacEventFeedback\DataContainer;

use Contao\CalendarEventsMemberModel;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Markocupic\SacEventFeedback\EventFeedbackHelper;

class TlCalendarEventsMember
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var array
     */
    private $onlineFeedbackConfigs;

    /**
     * @var EventFeedbackHelper
     */
    private $eventFeedbackHelper;

    public function __construct(ContaoFramework $framework, array $onlineFeedbackConfigs, EventFeedbackHelper $eventFeedbackHelper)
    {
        $this->framework = $framework;
        $this->onlineFeedbackConfigs = $onlineFeedbackConfigs;
        $this->eventFeedbackHelper = $eventFeedbackHelper;
    }

    /**
     * @Callback(table="tl_calendar_events_member", target="fields.hasParticipated.save")
     */
    public function onCompletedEvent(string $value, DataContainer $dc): string
    {
        $calendarEventsMemberModel = CalendarEventsMemberModel::findByPk($dc->id);
        $calendarEventsModel = CalendarEventsModel::findByPk($calendarEventsMemberModel->eventId);

        if (null === $calendarEventsMemberModel || null === $calendarEventsModel || !$this->eventFeedbackHelper->eventHasValidFeedbackConfiguration($calendarEventsModel)) {
            return $value;
        }

        // If $value === ''
        if (!$value && !$calendarEventsMemberModel->countOnlineEventFeedbackNotifications) {
            $this->eventFeedbackHelper->deleteFeedbackReminder($calendarEventsMemberModel);
        }

        // If $value === '1'
        if ($value && !$calendarEventsMemberModel->countOnlineEventFeedbackNotifications) {
            $this->eventFeedbackHelper->addFeedbackReminder($calendarEventsMemberModel);
        }

        // Return the processed value
        return $value;
    }
}
