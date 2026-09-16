<?php

declare(strict_types=1);

/*
 * Seeker-Theme — Inline-Icon-Feld für Content-Elements.
 *
 * Hängt eine `addIcon`-Checkbox an die konfigurierten Paletten — analog
 * zum `addCardImage`-Pattern. Ist die Checkbox aktiv, taucht die Subpalette
 * mit dem `inlineSvg`-Tile-Picker auf (siehe Widget/IconPickerWidget).
 *
 * Frontend rendert das Icon via `inline_icon()` (siehe
 * Twig\InlineIconExtension + ce_card.html.twig + content_element/text.html.twig).
 *
 * Für eine weitere Palette einfach den Key unten in
 * `$paletteWithInlineIcon` ergänzen.
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

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

// `addIcon` als Subpalette-Selector registrieren, damit die Checkbox die
// `inlineSvg`-Picker-Zeile ein-/ausblendet.
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addIcon';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addIcon'] = 'inlineSvg';

$GLOBALS['TL_DCA']['tl_content']['fields']['addIcon'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['addIcon'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['inlineSvg'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['inlineSvg'],
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
