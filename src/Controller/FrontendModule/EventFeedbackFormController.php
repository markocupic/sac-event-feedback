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

namespace Markocupic\SacEventFeedback\Controller\FrontendModule;

use Contao\CalendarEventsMemberModel;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * Class EventFeedbackFormController.
 *
 * @FrontendModule(EventFeedbackFormController::TYPE, category="event_feedback", template="mod_event_feedback_form")
 */
class EventFeedbackFormController extends AbstractFrontendModuleController
{
    public const TYPE = 'event_feedback_form';
    public const UUID_TEST = 'b6d3ea2b-d8c4-4aa7-9045-0eb499503e1d';

    private Security $security;
    private EventFeedbackHelper $eventFeedbackHelper;

    /**
     * @var PageModel
     */
    private $page;

    /**
     * @var FrontendUser
     */
    private $user;

    /**
     * @var CalendarEventsMemberModel
     */
    private $objEventRegistration;

    public function __construct(Security $security, EventFeedbackHelper $eventFeedbackHelper)
    {
        $this->security = $security;
        $this->eventFeedbackHelper = $eventFeedbackHelper;
    }

    public function __invoke(Request $request, ModuleModel $model, string $section, array $classes = null, PageModel $page = null): Response
    {
        // Get the page model
        $this->page = $page;

        // Get logged in user
        $this->user = $this->security->getUser();

        if (!$this->user instanceof FrontendUser) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $this->template = $template;
        $this->template->hasError = false;

        $uuid = $request->query->get('uuid');

        $member = CalendarEventsMemberModel::findOneByUuid($uuid);

        $this->template->error = null;

        if (null === $member || $member->sacMemberId !== $this->user->sacMemberId) {
            return $this->returnWithError('Die UUID passt nicht zum eingeloggten Benutzer.');
        }

        if (null === ($event = CalendarEventsModel::findByPk($member->eventId))) {
            return $this->returnWithError('Der zur UUID passende Event wurde nicht gefunden.');
        }

        if (null === CalendarModel::findByPk($event->pid)) {
            return $this->returnWithError('Der zur UUID passende Kalender wurde nicht gefunden.');
        }

        if (null === ($form = $this->eventFeedbackHelper->getForm($event))) {
            return $this->returnWithError('Das zur UUID passende Formular wurde nicht gefunden.');
        }

        if (null !== EventFeedbackModel::findOneByUuid($uuid)) {
            return $this->returnWithError('Das Formular ist bereits ausgefüllt worden.');
        }

        $this->template->form = Controller::getForm($form->id);
        $this->template->member = $member->row();
        $this->template->event = $event->row();

        return $this->template->getResponse();
    }

    private function returnWithError(string $errMsg): Response
    {
        $this->template->hasError = true;
        $this->template->error = $errMsg;

        return $this->template->getResponse();
    }
}
