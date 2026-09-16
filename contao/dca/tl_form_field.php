<?php

declare(strict_types=1);

/*
 * Seeker-Theme — DCA-Erweiterung für tl_form_field.
 *
 * Formularfeld „Stellen-Info" (apply_job_info): zeigt die per `?stelle=`
 * gewählte Stelle als Karte im Bewerbungsformular und überträgt sie über ein
 * verstecktes Feld unter dem Feldnamen (z. B. „stelle"): im HTML der Alias,
 * gespeichert werden Stellentitel und Standort. Ohne Stelle erscheint ein
 * Freitextfeld für eine Initiativbewerbung — dafür gelten Pflichtfeld und
 * Platzhalter. Die Feldbezeichnung wird nicht angezeigt, beschriftet aber
 * Zusammenfassung und E-Mail.
 */

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['apply_job_info'] = '{type_legend},type,name,label;{fconfig_legend},mandatory,placeholder;{template_legend:hide},customTpl;{expert_legend:hide},class';
