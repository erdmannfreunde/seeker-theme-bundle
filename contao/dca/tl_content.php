<?php

declare(strict_types=1);

/*
 * Seeker-Theme — DCA-Erweiterung für tl_content.
 *
 * - Icon (addIcon + inlineSvg) für Text und Karte, analog zum
 *   addCardImage-Pattern. Der Tile-Picker kommt aus Widget/IconPickerWidget,
 *   das Frontend rendert über inline_icon() (Twig\InlineIconExtension,
 *   ce_card.html.twig, content_element/text.html.twig).
 * - Dachzeile (supline aus dem Nutshell core-bundle) für „Liste“
 * - Inhaltselemente „Zitat“, „Badge“, „Standort“ und „Auszeichnungen“
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\System;
use Nutshell\IconPicker\Icon\IconSetResolver;

/*
 * Paletten
 */

// Icon: für weitere Paletten einen weiteren Block ergänzen
PaletteManipulator::create()
    ->addLegend('icon_legend', 'image_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('addIcon', 'icon_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('text', 'tl_content')
;

PaletteManipulator::create()
    ->addLegend('icon_legend', 'type_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('addIcon', 'icon_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('card', 'tl_content')
;

// Dachzeile, z. B. „Deine Aufgaben“ über den Listen der Stellen-Detailseite
PaletteManipulator::create()
    ->addField('supline', 'type')
    ->applyToPalette('list', 'tl_content')
;

// Zitat (QuoteController): Bildoptionen wie beim Text-Element über die addImage-Subpalette des Cores
$GLOBALS['TL_DCA']['tl_content']['palettes']['quote'] = '{type_legend},type,headline;{text_legend},quote_text,quote_author;{image_legend},addImage;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';

// Badge (BadgeController): kurze Hinweis- oder Kennzahl-Karte
$GLOBALS['TL_DCA']['tl_content']['palettes']['badge'] = '{type_legend},type;{badge_legend},badge_headline,badge_value,badge_note;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';

// Standort (LocationController)
$GLOBALS['TL_DCA']['tl_content']['palettes']['location'] = '{type_legend},type,supline,headline;{text_legend},text;{image_legend},singleSRC,size;{location_facts_legend},location_facts;{link_legend},location_jobs_url,location_jobs_text,location_map_url,location_map_text;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';

// Auszeichnungen (AwardsController): das Template setzt die Anzahl der Einträge als Klasse (awards--items-4)
$GLOBALS['TL_DCA']['tl_content']['palettes']['awards'] = '{type_legend},type,headline;{awards_legend},awards_items;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';

/*
 * Subpaletten
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addIcon';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addIcon'] = 'inlineSvg';

/*
 * Felder (Labels setzt Contao automatisch aus $GLOBALS['TL_LANG']['tl_content'])
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['addIcon'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['inlineSvg'] = [
    'exclude' => true,
    'inputType' => 'iconPicker',
    'eval' => [
        // Pflichtfeld nur effektiv, wenn addIcon aktiv ist — Contao validiert
        // versteckte Subpalette-Felder nicht.
        'mandatory' => true,
        'tl_class' => 'clr',
    ],
    'sql' => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['quote_text'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['mandatory' => true, 'basicEntities' => true, 'tl_class' => 'clr'],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['quote_author'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['badge_headline'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 40, 'basicEntities' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

// Einfaches HTML erlaubt, <strong> wird groß dargestellt
$GLOBALS['TL_DCA']['tl_content']['fields']['badge_value'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'maxlength' => 80, 'allowHtml' => true, 'tl_class' => 'clr w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['badge_note'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 60, 'basicEntities' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

// Zeilen aus Icon + Text im rowWizard des Cores, gemeinsam für die Fakten im
// Standort und die Einträge der Auszeichnungen
$seekerIconTextRows = [
    'icon' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['icon_row_icon'],
        'inputType' => 'select',
        'options_callback' => static fn (): array => array_keys(
            System::getContainer()->get(IconSetResolver::class)->listAll(),
        ),
        // cell_style setzt der rowWizard an die Tabellenzelle; ohne Breite teilen sich Icon und Text die Zeile
        'eval' => ['includeBlankOption' => true, 'cell_style' => 'width: 12rem'],
    ],
    'text' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['icon_row_text'],
        'inputType' => 'text',
        'eval' => ['maxlength' => 80],
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['location_facts'] = [
    'exclude' => true,
    'inputType' => 'rowWizard',
    'fields' => $seekerIconTextRows,
    'eval' => ['tl_class' => 'clr'],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['location_jobs_url'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'w50'],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['location_jobs_text'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['location_map_url'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'clr w50'],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['location_map_text'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['awards_items'] = [
    'exclude' => true,
    'inputType' => 'rowWizard',
    'fields' => $seekerIconTextRows,
    'eval' => ['tl_class' => 'clr'],
    'sql' => 'text NULL',
];

/*
 * Callbacks
 */

// Standort: nur Bilder im Dateiwähler, wie beim Bild-Element des Cores
$GLOBALS['TL_DCA']['tl_content']['fields']['singleSRC']['load_callback'][] = static function ($value, DataContainer $dc) {
    if ('location' === $dc->activeRecord?->type) {
        $GLOBALS['TL_DCA']['tl_content']['fields']['singleSRC']['eval']['extensions'] = '%contao.image.valid_extensions%';
    }

    return $value;
};
