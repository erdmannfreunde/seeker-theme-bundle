<?php

declare(strict_types=1);

/*
 * Seeker-Theme — DCA-Erweiterung für tl_module.
 *
 * Plenta blendet beim Stellen-Reader keine Weiterleitungsseite ein. Das
 * Reader-Template nutzt jumpTo als Bewerbungsseite für den Button
 * „Jetzt bewerben" ({{link_url::jumpTo}}?stelle={alias}).
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('redirect_legend', 'config_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('jumpTo', 'redirect_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('plenta_jobs_basic_offer_reader', 'tl_module')
;
