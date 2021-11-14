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

namespace Markocupic\SacEventFeedback\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Markocupic\SacEventFeedback\EventFeedbackHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;

/**
 * Class MyCustomController.
 *
 * @Route("/reminder",
 *     name="MyCustomController::class",
 *     defaults={
 *         "_scope" = "frontend",
 *         "_token_check" = true
 *     }
 * )
 */
class MyCustomController extends AbstractController
{
    /**
     * @var TwigEnvironment
     */
    private $twig;

    private $framework;

    /**
     * @var EventFeedbackHelper
     */
    private $eventFeedbackHelper;

    /**
     * MyCustomController constructor.
     */
    public function __construct(TwigEnvironment $twig, ContaoFramework $framework, EventFeedbackHelper $eventFeedbackHelper)
    {
        $this->twig = $twig;
        $this->framework = $framework;
        $this->eventFeedbackHelper = $eventFeedbackHelper;
    }

    /**
     * Generate the response.
     */
    public function __invoke()
    {
        $this->framework->initialize(true);
        $this->eventFeedbackHelper->sendReminder();

        $animals = [
            [
                'species' => 'dogs',
                'color' => 'white',
            ],
            [
                'species' => 'birds',
                'color' => 'black',
            ], [
                'species' => 'cats',
                'color' => 'pink',
            ], [
                'species' => 'cows',
                'color' => 'yellow',
            ],
        ];

        return new Response($this->twig->render(
            '@MarkocupicSacEventFeedback/MyCustom/my_custom.html.twig',
            [
                'animals' => $animals,
            ]
        ));
    }
}
