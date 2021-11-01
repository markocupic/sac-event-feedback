<?php

declare(strict_types=1);

/*
 * This file is part of SAC Event Evaluation Bundle.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/sac-event-evaluatio
 */

namespace Markocupic\SacEventEvaluation\Controller\FrontendModule;

use Contao\CalendarEventsMemberModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Date;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Contao\Template;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EventEvaluationFormController.
 *
 * @FrontendModule(EventEvaluationFormController::TYPE, category="event_evaluation", template="mod_event_evaluation_form")
 */
class EventEvaluationFormController extends AbstractFrontendModuleController
{
    public const TYPE = 'event_evaluation_form';
    public const UUID_TEST = 'b6d3ea2b-d8c4-4aa7-9045-0eb499503e1d';

    /**
     * @var PageModel
     */
    private $page;

    /**
     * @var CalendarEventsMemberModel
     */
    private $objEventRegistration;

    public function __invoke(Request $request, ModuleModel $model, string $section, array $classes = null, PageModel $page = null): Response
    {
        // Get the page model
        $this->page = $page;

        /*
         * @todo remove this line
         */
        $request->query->set('uuid', self::UUID_TEST);

        $uuid = $request->query->get('uuid');

        $arrReminder = System::getContainer()->getParameter('markocupic_sac_event_evaluation.configurations');
        die(print_r($arrReminder,true));

        if (null === ($this->objEventRegistration = CalendarEventsMemberModel::findByUuid($uuid))) {
            return new Response('Invalid request.');
        }

        if ($this->page instanceof PageModel && $this->get('contao.routing.scope_matcher')->isFrontendRequest($request)) {
            // If TL_MODE === 'FE'
            //$this->page->loadDetails();
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
        $template->form = Controller::getForm($model->form);

        return $template->getResponse();
    }

    /**
     * Generate the module.
     */
    protected function gettResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $userFirstname = 'DUDE';
        $user = $this->get('security.helper')->getUser();

        if ($user instanceof FrontendUser) {
            $userFirstname = $user->firstname;
        }

        /** @var Session $session */
        $session = $request->getSession();
        $feBag = $session->getBag('contao_frontend');
        $feBag->set('foo', 'bar');

        /** @var Date $dateAdapter */
        $dateAdapter = $this->get('contao.framework')->getAdapter(Date::class);
        $intWeekday = $dateAdapter->parse('w');
        $translator = $this->get('translator');
        $strWeekday = $translator->trans('DAYS.'.$intWeekday, [], 'contao_default');

        $arrGuests = [];
        $stmt = $this->get('database_connection')
            ->executeQuery(
                'SELECT * FROM tl_member WHERE gender=? ORDER BY lastname',
                ['female']
            )
        ;

        while (false !== ($objMember = $stmt->fetch(\PDO::FETCH_OBJ))) {
            $arrGuests[] = $objMember->firstname;
        }

        $template->helloTitle = sprintf(
            'Hi %s, and welcome to the "Hello World Module". Today is %s.',
            $userFirstname,
            $strWeekday
        );

        $template->helloText = 'Our guests today are: '.implode(', ', $arrGuests);

        return $template->getResponse();
    }
}
