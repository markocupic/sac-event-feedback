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
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * Lazyload some services.
     */
    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();

        $services['contao.framework'] = ContaoFramework::class;
        $services['database_connection'] = Connection::class;
        $services['contao.routing.scope_matcher'] = ScopeMatcher::class;
        $services['security.helper'] = Security::class;
        $services['translator'] = TranslatorInterface::class;

        return $services;
    }

    /**
     * Generate the module.
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $uuid = $request->query->get('uuid');

        $member = CalendarEventsMemberModel::findByUuid($uuid);

        $template->error = null;

        if (null === $member || $member->sacMemberId !== $this->user->sacMemberId) {
            $template->error = 'Die UUID passt nicht zum eingeloggten Benutzer.';
        }

        if (null === ($event = CalendarEventsModel::findByPk($member->eventId))) {
            $template->error = 'Der zur UUID passende Event wurde nicht gefunden.';
        }

        if (null === ($calendar = CalendarModel::findByPk($event->pid))) {
            $template->error = 'Der zur UUID passende Kalender wurde nicht gefunden.';
        }

        if (null !== $event) {
            $form = $this->eventFeedbackHelper->getForm($event);

            if (null === $form) {
                $template->error = 'Das zur UUID passende Formular wurde nicht gefunden.';
            }else{
                $template->form = Controller::getForm($form->id);
            }
        }

        $template->firstname = $member->firstname;
        $template->lastname = $member->firstname;

        return $template->getResponse();
    }
}
