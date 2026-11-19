<?php

declare(strict_types=1);

/*
 * Seeker-Theme — DCA-Erweiterung für Plenta Jobs Basic.
 *
 * Ergänzt am Job-Offer zwei Theme-spezifische Felder, mit denen das
 * Reader-Template (Stellen-Detailseite) im Hero den Bereich-Pill und den
 * Remote-Anteil anzeigt.
 *
 *   flowDept        Bereich-Pill (Eyebrow im Hero)
 *   flowRemote      Remote-Anteil (Meta-Karte, optional)
 *
 * Bewerbungsprozess + Kontakt+CTA sind theme-global und kommen NICHT aus
 * dem Job — siehe Reader-Template bzw. das CE `contact`.
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('flow_legend', 'meta_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField(['flowDept', 'flowRemote'], 'flow_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_plenta_jobs_basic_offer')
;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['flowDept'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['flowRemote'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];
