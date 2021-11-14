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

namespace Markocupic\SacEventFeedback\Model;

use Contao\Model;

/**
 * Class EventFeedbackReminderModel.
 */
class EventFeedbackReminderModel extends Model
{
    protected static $strTable = 'tl_event_feedback_reminder';
}
