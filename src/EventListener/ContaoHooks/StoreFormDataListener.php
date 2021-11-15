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

use Contao\Database;
use Contao\Form;
use Contao\FrontendUser;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class StoreFormDataListener
{
    public const TYPE = 'storeFormData';
    public const PRIORITY = 100;

    private Security $security;
    private EventFeedbackHelper $eventFeedbackHelper;
    private RequestStack $requestStack;

    public function __construct(Security $security, EventFeedbackHelper $eventFeedbackHelper, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->eventFeedbackHelper = $eventFeedbackHelper;
        $this->requestStack = $requestStack;
    }

    /**
     * Add uuid,tstamp,dateAdded and pid to $submittedData.
     *
     * @throws \Exception
     */
    public function augmentData(array $data, Form $form): array
    {
        // Get logged in user
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        if (!$form->isSacEventFeedbackForm) {
            return $data;
        }

        if (!$request->query->has('uuid') || !$user instanceof FrontendUser) {
            throw new \Exception('The form is only accessible to logged in contao frontend users.');
        }

        if (null !== EventFeedbackModel::findByUuid($request->query->has('uuid'))) {
            throw new \Exception('The record with tl_event_feedback.uuid allready exists.');
        }

        $member = $this->eventFeedbackHelper->getFrontendUserFromUuid($request->query->get('uuid'));

        if (null === $member || (int) $member->id !== (int) $user->id) {
            return $data;
        }

        $uuid = $request->query->get('uuid');
        $event = $this->eventFeedbackHelper->getEventFromUuid($uuid);

        $data['uuid'] = $request->query->get('uuid');
        $data['pid'] = $event->id;
        $data['dateAdded'] = time();
        $data['tstamp'] = time();

        // Delete no more used reminders
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback_reminder WHERE uuid=?')
            ->execute($uuid)
        ;

        return $data;
    }
}
