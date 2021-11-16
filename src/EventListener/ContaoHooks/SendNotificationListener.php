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

use Contao\CoreBundle\ServiceAnnotation\Hook;
use NotificationCenter\Model\Message;

/**
 * @Hook("SendNotificationListener::TYPE", priority=SendNotificationListener::PRIORITY)
 */
class SendNotificationListener
{
    public const TYPE = 'sendNotification';
    public const PRIORITY = 100;

    public function __invoke(Message $objMessage, array &$arrTokens, string $language, $objGatewayModel): bool
    {
        return true;
    }
}
