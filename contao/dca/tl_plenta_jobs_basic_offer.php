<?php

declare(strict_types=1);

/*
 * Seeker-Theme — DCA-Erweiterung für Plenta Jobs Basic.
 *
 * Ergänzt am Job-Offer fünf Theme-spezifische Felder, mit denen das
 * Reader-Template (Stellen-Detailseite) die strukturierten Sections
 * Aufgaben · Profil · Wir bieten füllt und im Hero den Bereich-Pill
 * und den Remote-Anteil anzeigt.
 *
 *   flowDept        Bereich-Pill (Eyebrow im Hero)
 *   flowRemote      Remote-Anteil (Meta-Karte, optional)
 *   flowTasks       Bullets "Was dich erwartet" — eine Zeile = ein Bullet
 *   flowProfile     Bullets "Was du mitbringst"
 *   flowConditions  Bullets "Wir bieten" (Konditionen DIESER Stelle)
 *
 * Bewerbungsprozess + Kontakt+CTA sind theme-global und kommen NICHT aus
 * dem Job — siehe Reader-Template bzw. das CE `contact`.
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('flow_legend', 'meta_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField(['flowDept', 'flowRemote', 'flowTasks', 'flowProfile', 'flowConditions'], 'flow_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_plenta_jobs_basic_offer')
;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['flowDept'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['flowDept'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['flowRemote'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['flowRemote'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['flowTasks'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['flowTasks'],
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['rows' => 6, 'style' => 'height:140px', 'tl_class' => 'clr'],
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['flowProfile'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['flowProfile'],
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['rows' => 6, 'style' => 'height:140px', 'tl_class' => 'clr'],
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['flowConditions'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['flowConditions'],
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['rows' => 6, 'style' => 'height:160px', 'tl_class' => 'clr'],
    'sql' => 'mediumtext NULL',
];
