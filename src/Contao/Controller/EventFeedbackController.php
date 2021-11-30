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

namespace Markocupic\SacEventFeedback\Contao\Controller;

use Contao\BackendUser;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\InvalidResourceException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\DataContainer;
use Contao\EventReleaseLevelPolicyModel;
use Contao\Input;
use Markocupic\SacEventFeedback\Feedback\Feedback;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as TwigEnvironment;

class EventFeedbackController
{
    private ContaoFramework $framework;
    private Security $security;
    private TwigEnvironment $twig;
    private array $arrFeedback = [];

    public function __construct(ContaoFramework $framework, Security $security, TwigEnvironment $twig)
    {
        $this->framework = $framework;
        $this->security = $security;
        $this->twig = $twig;
    }

    public function getEventFeedbackAction(DataContainer $dc): Response
    {
        $id = Input::get('id');
        $event = CalendarEventsModel::findByPk($id);

        if (null === $event) {
            throw new InvalidResourceException(sprintf('Event with id %s not found.', $id));
        }

        if (!$this->isAllowed($event)) {
            throw new AccessDeniedException('User is not allowed to access the backend module "sac_calendar_events_tool".');
        }

        $objFeedback = new Feedback($event);

        return new Response($this->twig->render(
            '@MarkocupicSacEventFeedback/sac_event_feedback.html.twig',
            [
                'event' => $objFeedback->getEvent(false)->row(),
                'has_feedbacks' => $objFeedback->countFeedbacks(false) > 0 ? true : false,
                'feedbacks' => $objFeedback->getDataAll(false),
                'feedback_count' => $objFeedback->countFeedbacks(false),
            ]
        ));
    }

    private function isAllowed(CalendarEventsModel $event): bool
    {
        $user = $this->security->getUser();

        if ($user instanceof BackendUser) {
            $user = $this->security->getUser();

            if (!$user instanceof BackendUser) {
                return false;
            }

            // Apply same permission rules like "teilnehmerliste"
            $hasPermissionsWatchingFeedbacks = true;

            if (!EventReleaseLevelPolicyModel::hasWritePermission($user->id, $event->id) && (int) $event->registrationGoesTo !== (int) $user->id) {
                $hasPermissionsWatchingFeedbacks = false;
            }

            $canAccessModule = $this->security->isGranted(
                ContaoCorePermissions::USER_CAN_ACCESS_MODULE,
                'sac_calendar_events_tool'
            );

            if ($canAccessModule && $hasPermissionsWatchingFeedbacks) {
                return true;
            }
        }

        return false;
    }
}
