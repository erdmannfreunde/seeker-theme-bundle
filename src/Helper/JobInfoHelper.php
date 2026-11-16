<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\Helper;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Plenta\ContaoJobsBasic\Helper\NumberHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Daten der Job-Info-Karte im Bewerbungsformular.
 *
 * Lädt den Plenta-Job-Offer zum Alias und liefert Bereich (flowDept), Titel
 * und Meta (Standort, Pensum, Gehalt, Remote-Anteil). Öffentlicher Service
 * (config/services.yaml), weil das Formularfeld-Widget nicht per Autowiring
 * entsteht und sich den Helper aus dem Container holt.
 */
class JobInfoHelper
{
    public function __construct(
        private readonly MetaFieldsHelper $metaFieldsHelper,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Gehalt wie im Plenta-Reader: Plenta speichert Beträge in der kleinsten
     * Währungseinheit (Cent), NumberHelper rechnet um und formatiert nach
     * Locale, die Einheit (Monat, Jahr …) kommt aus Plentas Übersetzung.
     */
    public function formatSalary(PlentaJobsBasicOfferModel $offer): string|null
    {
        if (!$offer->addSalary) {
            return null;
        }

        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'de';
        $numberHelper = new NumberHelper($offer->salaryCurrency ?: 'EUR', $locale);

        $parts = [];
        foreach ([$offer->salaryValue, $offer->salaryMaxValue] as $value) {
            if ((int) $value > 0) {
                $parts[] = $numberHelper->formatCurrency((int) $value);
            }
        }

        if (!$parts) {
            return null;
        }

        $salary = implode(' – ', $parts);

        if ($offer->salaryUnit) {
            $salary .= ' / '.$this->translator->trans('tl_plenta_jobs_basic_offer.salaryUnits.'.$offer->salaryUnit, [], 'contao_tl_plenta_jobs_basic_offer');
        }

        return $salary;
    }

    /**
     * @return array{title: string, dept: string|null, location: string|null, employment: string|null, remote: string|null, salary: string|null}|null
     */
    public function getInfo(string $alias): array|null
    {
        $offer = PlentaJobsBasicOfferModel::findPublishedByIdOrAlias($alias);
        if (null === $offer) {
            return null;
        }

        $meta = $this->metaFieldsHelper->getMetaFields($offer);

        return [
            'title'      => $meta['title'] ?? $offer->title,
            'dept'       => $offer->flowDept ?: null,
            'location'   => $meta['locationFormatted'] ?? null,
            'employment' => $meta['employmentTypeFormatted'] ?? null,
            'remote'     => $offer->flowRemote ?: null,
            'salary'     => $this->formatSalary($offer),
        ];
    }
}
