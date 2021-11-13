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

namespace Markocupic\SacEventEvaluation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;

/**
 * Class MyCustomController.
 *
 * @Route("/my_custom22",
 *     name="markocupic_sac_event_evaluation_my_custom",
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

    /**
     * MyCustomController constructor.
     */
    public function __construct(TwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Generate the response.
     */
    public function __invoke()
    {
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
            '@MarkocupicSacEventEvaluation/MyCustom/my_custom.html.twig',
            [
                'animals' => $animals,
            ]
        ));
    }
}
