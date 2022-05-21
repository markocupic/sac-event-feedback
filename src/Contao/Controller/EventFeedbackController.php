<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Feedback.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-feedback
 */

namespace Markocupic\SacEventFeedback\Contao\Controller;

use Contao\BackendUser;
use Contao\CalendarEventsMemberModel;
use Contao\CalendarEventsModel;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\InvalidResourceException;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\DataContainer;
use Contao\Date;
use Contao\EventReleaseLevelPolicyModel;
use Contao\Input;
use Haste\Util\Url;
use Markocupic\CloudconvertBundle\Conversion\ConvertFile;
use Markocupic\PhpOffice\PhpWord\MsWordTemplateProcessor;
use Markocupic\SacEventFeedback\Feedback\Feedback;
use Markocupic\SacEventToolBundle\CalendarEventsHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as TwigEnvironment;

class EventFeedbackController
{
    private Security $security;
    private TwigEnvironment $twig;
    private ConvertFile $convertFile;
    private string $docxTemplate;
    private string $projectDir;

    public function __construct(Security $security, TwigEnvironment $twig, ConvertFile $convertFile, string $docxTemplate, string $projectDir)
    {
        $this->security = $security;
        $this->twig = $twig;
        $this->convertFile = $convertFile;
        $this->projectDir = $projectDir;
        $this->docxTemplate = $docxTemplate;
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
                'pdf_link' => '/'.Url::addQueryString('key=showEventFeedbacksAsPdf'),
            ]
        ));
    }

    public function getEventFeedbackAsPdfAction(DataContainer $dc): Response
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

        // Create phpword instance
        $targetSrc = sprintf('system/tmp/event_feedback_%s_%s.docx', $event->id, time());
        $countReg = CalendarEventsMemberModel::countBy(['hasParticipated=?', 'eventId=?'], ['1', $event->id]);

        $objPhpWord = new MsWordTemplateProcessor($this->docxTemplate, $targetSrc);
        $objPhpWord->replace('event_title', htmlspecialchars(html_entity_decode((string) $event->title)));
        $objPhpWord->replace('event_type', $event->eventType);
        $objPhpWord->replace('event_id', $event->id);
        $objPhpWord->replace('event_instructor', CalendarEventsHelper::getMainInstructorName($event));
        $arrEventDates = array_map(static fn ($tstamp) => Date::parse('d.m.Y', $tstamp), CalendarEventsHelper::getEventTimestamps($event));
        $objPhpWord->replace('event_date', implode("\r\n", $arrEventDates), ['multiline' => true]);
        $objPhpWord->replace('date', Date::parse('d.m.Y'));
        $objPhpWord->replace('count_fb', $objFeedback->countFeedbacks());
        $objPhpWord->replace('count_reg', (string) $countReg);

        // Dropdowns
        foreach ($objFeedback->getDropdowns() as $arrDropdown) {
            $objPhpWord->createClone('dropdown_label');
            $label = htmlspecialchars(html_entity_decode((string) $arrDropdown['label']));
            $objPhpWord->addToClone('dropdown_label', 'dropdown_label', $label, ['multiline' => true]);

            $dropdownText = '';

            foreach ($arrDropdown['values'] as $value) {
                $dropdownText .= sprintf('%sx %s'."\r\n", $value['count'], $value['label']);
            }

            $dropdownText = htmlspecialchars(html_entity_decode((string) $dropdownText));
            $objPhpWord->addToClone('dropdown_label', 'dropdown_feedback', $dropdownText, ['multiline' => true]);
        }

        // Textareas
        foreach ($objFeedback->getTextareas() as $arrFeedback) {
            $objPhpWord->createClone('text_label');
            $label = htmlspecialchars(html_entity_decode((string) $arrFeedback['label']));

            $objPhpWord->addToClone('text_label', 'text_label', $label, ['multiline' => true]);
            $text = implode("\r\n\r\n", $arrFeedback['values']);
            $text = htmlspecialchars(html_entity_decode((string) $text));

            $objPhpWord->addToClone('text_label', 'text_feedback', $text, ['multiline' => true]);
        }

        $objPhpWord->sendToBrowser(false)
            ->generateUncached(true)
            ->generate()
        ;

        throw new ResponseException($this->convertFile->file($this->projectDir.'/'.$targetSrc)->sendToBrowser(true)->uncached(true)->convertTo('pdf'));
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
