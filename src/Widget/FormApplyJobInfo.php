<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\Widget;

use Contao\BackendTemplate;
use Contao\System;
use Contao\Widget;
use ErdmannFreunde\SeekerThemeBundle\Helper\JobInfoHelper;

/**
 * Formularfeld „Stellen-Info“ (Typ apply_job_info).
 *
 * Mit gültigem `?stelle=…` zeigt es die Stelle als Karte im Formular (wird
 * so bei Ajax-Schrittwechseln mitgeliefert) und überträgt sie über ein
 * verstecktes Feld unter dem Feldnamen: im HTML der Alias, gespeichert
 * werden Stellentitel und Standort.
 *
 * Ohne gültigen Alias wird daraus eine Initiativbewerbung: ein Freitextfeld
 * für die selbst bezeichnete Wunschstelle, gespeichert als
 * „Initiativbewerbung: …“. Pflichtfeld und Platzhalter kommen aus dem
 * Formulargenerator.
 */
class FormApplyJobInfo extends Widget
{
    protected $strTemplate = 'form_apply_job_info';

    protected $strPrefix = 'widget widget-apply-job-info';

    protected $blnSubmitInput = true;

    /**
     * Pflichtfeld und Platzhalter wie beim Core-Textfeld (FormText) als
     * Attribute des Freitextfelds.
     */
    public function __set($strKey, $varValue)
    {
        switch ($strKey) {
            case 'mandatory':
                if ($varValue) {
                    $this->arrAttributes['required'] = 'required';
                } else {
                    unset($this->arrAttributes['required']);
                }
                parent::__set($strKey, $varValue);
                break;

            case 'placeholder':
                $this->arrAttributes[$strKey] = $varValue;
                break;

            default:
                parent::__set($strKey, $varValue);
        }
    }

    public function parse($arrAttributes = null)
    {
        $container = System::getContainer();
        $request = $container->get('request_stack')->getCurrentRequest();

        if ($request && $container->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = '### ' . $GLOBALS['TL_LANG']['FFL']['apply_job_info'][0] . ' ###';

            return $template->parse();
        }

        $alias = trim((string) $request?->query->get('stelle', ''));
        $this->jobInfo = '' !== $alias ? $container->get(JobInfoHelper::class)->getInfo($alias) : null;

        if (null !== $this->jobInfo) {
            $this->jobAlias = $alias;
        } else {
            // mp_forms belegt beim Schritt zurück den gespeicherten Wert vor —
            // ins Freitextfeld gehört er ohne das Präfix.
            $value = (string) $this->value;
            $prefix = self::initiativePrefix();
            $this->initiativeText = str_starts_with($value, $prefix) ? substr($value, \strlen($prefix)) : $value;
        }

        return parent::parse($arrAttributes);
    }

    /**
     * Stelle: Der Wert kommt aus einem versteckten Feld und ist damit
     * manipulierbar — geprüft wird der Alias (nur veröffentlichte Stellen),
     * übernommen werden Stellentitel und Standort, lesbar in Zusammenfassung
     * und E-Mail und eindeutig, wenn es dieselbe Stelle an mehreren Standorten
     * gibt. Alles andere ist der Freitext einer Initiativbewerbung. mp_forms
     * speichert diesen validierten Wert und validiert ihn nicht erneut.
     */
    protected function validator($varInput)
    {
        $input = trim((string) $varInput);
        $info = '' !== $input ? System::getContainer()->get(JobInfoHelper::class)->getInfo($input) : null;

        if (null !== $info) {
            return $info['location'] ? $info['title'] . ' – ' . $info['location'] : $info['title'];
        }

        // Initiativbewerbung: Pflichtfeld-Prüfung der Basisklasse
        $text = (string) parent::validator($input);

        return '' !== $text ? self::initiativePrefix() . $text : '';
    }

    public function generate()
    {
        return '';
    }

    private static function initiativePrefix(): string
    {
        return ($GLOBALS['TL_LANG']['MSC']['seeker_initiative'] ?? 'Initiativbewerbung') . ': ';
    }
}
