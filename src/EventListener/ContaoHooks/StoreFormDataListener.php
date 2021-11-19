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
use Contao\Database;
use Contao\Form;
use Contao\FrontendUser;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

/**
 * @Hook(StoreFormDataListener::TYPE, priority=StoreFormDataListener::PRIORITY)
 */
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
     * Add uuid,tstamp,dateAdded and pid to $arrData.
     *
     * @throws \Exception
     */
    public function __invoke(array $arrData, Form $form): array
    {
        // Get logged in user
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        if (!$form->isSacEventFeedbackForm) {
            return $arrData;
        }

        if (!$request->query->has('event-reg-uuid') || !$user instanceof FrontendUser) {
            throw new \Exception('The form is only accessible to logged in contao frontend users.');
        }

        if (null !== EventFeedbackModel::findOneByUuid($request->query->has('event-reg-uuid'))) {
            throw new \Exception('The record with tl_event_feedback.uuid allready exists.');
        }

        $member = $this->eventFeedbackHelper->getFrontendUserFromUuid($request->query->get('event-reg-uuid'));

        if (null === $member || (int) $member->id !== (int) $user->id) {
            return $arrData;
        }

        $uuid = $request->query->get('event-reg-uuid');
        $event = $this->eventFeedbackHelper->getEventFromUuid($uuid);

        // Augment $arrData
        $arrData['form'] = $form->id;
        $arrData['uuid'] = $uuid;
        $arrData['pid'] = $event->id;
        $arrData['dateAdded'] = time();
        $arrData['tstamp'] = time();

        // Delete no more used reminders
        Database::getInstance()
            ->prepare('DELETE FROM tl_event_feedback_reminder WHERE uuid=?')
            ->execute($uuid)
        ;

        // Store new uuid in the session flash bag
        $session = $request->getSession();

        if ($session->isStarted()) {
            $flashBag = $session->getFlashBag();
            $flashBag->set('insert_sac_event_feedback', $uuid);
        }

        return $arrData;
    }
}
