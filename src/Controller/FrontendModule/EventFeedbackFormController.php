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
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @FrontendModule(EventFeedbackFormController::TYPE, category="event_feedback", template="mod_event_feedback_form")
 */
class EventFeedbackFormController extends AbstractFrontendModuleController
{
    public const TYPE = 'event_feedback_form';
    public const MODE_ERROR = 'has_error';
    public const MODE_FORM = 'show_form';
    public const MODE_CHECKOUT = 'checkout';

    private Security $security;
    private TranslatorInterface $translator;
    private EventFeedbackHelper $eventFeedbackHelper;
    private ?PageModel $page = null;
    private ?FrontendUser $user = null;
    private ?CalendarEventsMemberModel $objEventRegistration = null;
    private string $mode;

    public function __construct(Security $security, TranslatorInterface $translator, EventFeedbackHelper $eventFeedbackHelper)
    {
        $this->security = $security;
        $this->translator = $translator;
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
        $this->mode = self::MODE_FORM;
        $this->template->mode = $this->mode;

        $uuid = $request->query->get('event-reg-uuid');
        $member = CalendarEventsMemberModel::findOneByUuid($uuid);
        $session = $request->getSession();

        if ($session->isStarted()) {
            $flashBag = $session->getFlashBag();

            if ($flashBag->has('insert_sac_event_feedback')) {
                if ($uuid === $flashBag->get('insert_sac_event_feedback')[0]) {
                    $this->mode = self::MODE_CHECKOUT;
                    $salutation = $this->translator->trans('MSC.sacEvFb.salutation'.ucfirst($member->gender), [], 'contao_default');
                    $this->template->salutation = $salutation;
                    $this->template->checkoutMsg = $this->translator->trans('MSC.sacEvFb.checkoutMsg', [$salutation, $member->firstname], 'contao_default');
                }
            }
        }

        if (null === $member || $member->sacMemberId !== $this->user->sacMemberId) {
            return $this->returnWithError($this->translator->trans('ERR.sacEvFb.invalidUuidForLoggedInUser', [], 'contao_default'));
        }

        if (null === ($event = CalendarEventsModel::findByPk($member->eventId))) {
            return $this->returnWithError($this->translator->trans('ERR.sacEvFb.eventMatchingUuidNotFound', [], 'contao_default'));
        }

        if (null === CalendarModel::findByPk($event->pid)) {
            return $this->returnWithError($this->translator->trans('ERR.sacEvFb.calendarMatchingUuidNotFound', [], 'contao_default'));
        }

        if (null === ($form = $this->eventFeedbackHelper->getForm($event))) {
            return $this->returnWithError($this->translator->trans('ERR.sacEvFb.formMatchingUuidNotFound', [], 'contao_default'));
        }

        if (self::MODE_CHECKOUT !== $this->mode && null !== EventFeedbackModel::findOneByUuid($uuid)) {
            return $this->returnWithError($this->translator->trans('ERR.sacEvFb.formAllreadyFilledOut', [], 'contao_default'));
        }

        if (self::MODE_FORM === $this->mode) {
            $this->template->form = Controller::getForm($form->id);
            $this->template->formLabels = json_encode($this->getFormLabels($form));
        }

        $this->template->mode = $this->mode;
        $this->template->member = $member->row();
        $this->template->event = $event->row();

        return $this->template->getResponse();
    }

    private function returnWithError(string $errMsg): Response
    {
        $this->mode = self::MODE_ERROR;
        $this->template->mode = $this->mode;
        $this->template->errorMsg = $errMsg;

        return $this->template->getResponse();
    }

    private function getFormlabels(FormModel $form): array
    {
        $ff = FormFieldModel::findByPid($form->id);
        $arrOpt = [];

        while ($ff->next()) {
            if (!\in_array($ff->type, ['select', 'radio', 'checkbox'], true)) {
                continue;
            }
            $ffOpt = StringUtil::deserialize($ff->options, true);

            if (!empty($ffOpt)) {
                foreach ($ffOpt as $opt) {
                    if ('' === $opt['value']) {
                        continue;
                    }
                    $arrOpt[$ff->name][$opt['value']] = $opt['label'];
                }
            }
        }

        return $arrOpt;
    }
}
