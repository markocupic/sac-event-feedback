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
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Input;
use Contao\StringUtil;
use Markocupic\SacEventFeedback\Model\EventFeedbackModel;
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

        $this->arrFeedback['optionFields'] = [];
        $this->arrFeedback['textareaFields'] = [];
        $arrFormFields = [];
        $intFbCount = 0;
        $feedback = EventFeedbackModel::findByPid($event->id);

        while ($feedback->next()) {
            if (null === ($form = FormModel::findByPk($feedback->form))) {
                continue;
            }

            if (null === ($formFields = FormFieldModel::findByPid($form->id))) {
                continue;
            }

            ++$intFbCount;

            while ($formFields->next()) {
                if ($formFields->invisible) {
                    continue;
                }

                if (\in_array($formFields->type, ['select', 'checkbox', 'radio'], true)) {
                    $strBelongsTo = 'optionFields';
                } elseif ('textarea' === $formFields->type) {
                    $strBelongsTo = 'textareaFields';
                }

                if ($strBelongsTo && !isset($this->arrFeedback[$strBelongsTo][$formFields->name])) {
                    $arrFormFields[] = $formFields->name;
                    $this->addFormFieldToCollection($formFields->current(), $strBelongsTo);
                }
            }

            // Add values to the data array
            $arrFeedback = $feedback->row();

            foreach ($arrFeedback as $key => $value) {
                if (!\in_array($key, $arrFormFields, true)) {
                    continue;
                }

                if ('' !== trim((string) $value)) {
                    if (is_numeric($value) && isset($this->arrFeedback['optionFields'][$key]['values'][$value])) {
                        ++$this->arrFeedback['optionFields'][$key]['values'][$value]['count'];
                    } elseif (isset($this->arrFeedback['textareaFields'][$key])) {
                        $this->arrFeedback['textareaFields'][$key]['values'][] = $value;
                    }
                }
            }
        }

        return new Response($this->twig->render(
            '@MarkocupicSacEventFeedback/sac_event_feedback.html.twig',
            [
                'event' => $event->row(),
                'has_feedbacks' => $intFbCount > 0 ? true : false,
                'feedbacks' => $this->arrFeedback,
                'feedback_count' => $intFbCount,
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

    private function addFormFieldToCollection(FormFieldModel $formField, $strBelongsTo = 'optionFields'): void
    {
        if ($formField->invisible || '' === $formField->name || isset($this->arrFeedback[$strBelongsTo][$formField->name])) {
            return;
        }

        if ('optionFields' === $strBelongsTo) {
            $arrSub = [];
            $arrSub['values'] = [];
            $arrSub['label'] = $formField->label;

            $arrOptions = StringUtil::deserialize($formField->options, true);

            foreach ($arrOptions as $option) {
                if ('' !== $option['value']) {
                    $arrSub['values'][$option['value']]['count'] = 0;
                    $arrSub['values'][$option['value']]['label'] = $option['label'];
                }
            }
            $this->arrFeedback[$strBelongsTo][$formField->name] = $arrSub;
        }

        if ('textareaFields' === $strBelongsTo) {
            $arrSub['values'] = [];
            $arrSub['label'] = $formField->label;
            $this->arrFeedback[$strBelongsTo][$formField->name] = $arrSub;
        }
    }
}
