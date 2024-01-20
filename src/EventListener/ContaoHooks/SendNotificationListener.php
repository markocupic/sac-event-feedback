<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\EventListener\ContaoHooks;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use NotificationCenter\Model\Message;

#[AsHook(SendNotificationListener::HOOK, priority: 100)]
class SendNotificationListener
{
    public const HOOK = 'sendNotification';

    public function __invoke(Message $objMessage, array &$arrTokens, string $language, $objGatewayModel): bool
    {
        return true;
    }
}
