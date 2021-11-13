<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Evaluation Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-evaluation
 */

namespace Markocupic\SacEventEvaluation\EventListener\ContaoHooks;

use Contao\CalendarEventsMemberModel;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Form;
use Markocupic\SacEventEvaluation\Controller\FrontendModule\EventEvaluationFormController;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook(StoreFormDataListener::TYPE, priority=StoreFormDataListener::PRIORITY)
 */
class StoreFormDataListener
{
    public const TYPE = 'storeFormData';
    public const PRIORITY = 100;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(array $data, Form $form): array
    {
        $request = $this->requestStack->getCurrentRequest();
        /*
         * @todo remove this line
         */
        $request->query->set('uuid', EventEvaluationFormController::UUID_TEST);

        $uuid = $request->query->get('uuid', null);

        if (empty($uuid) || !$form->isSacEventEvaluationForm) {
            return $data;
        }

        if (null === ($objRegistration = CalendarEventsMemberModel::findByUuid($request->query->get('uuid')))) {
            return $data;
        }

        $data['uuid'] = $uuid;
        $data['pid'] = $objRegistration->eventId;
        $data['dateAdded'] = time();
        $data['tstamp'] = time();

        return $data;
    }
}
