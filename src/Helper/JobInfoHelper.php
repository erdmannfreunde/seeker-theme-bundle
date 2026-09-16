<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\Helper;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;

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
    ) {
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

        // Salary-String selbst zusammenbauen — der Helper liefert ihn nicht
        // direkt; wir spiegeln nur, was sonst auf der Detail-Seite zu sehen ist.
        $salary = null;
        if ($offer->addSalary) {
            $parts = [];
            if ($offer->salaryValue > 0)    { $parts[] = number_format((float) $offer->salaryValue, 0, ',', '.'); }
            if ($offer->salaryMaxValue > 0) { $parts[] = number_format((float) $offer->salaryMaxValue, 0, ',', '.'); }
            if ($parts) {
                $salary = implode(' – ', $parts) . ' ' . ($offer->salaryCurrency ?: 'EUR');
            }
        }

        return [
            'title'      => $meta['title'] ?? $offer->title,
            'dept'       => $offer->flowDept ?: null,
            'location'   => $meta['locationFormatted'] ?? null,
            'employment' => $meta['employmentTypeFormatted'] ?? null,
            'remote'     => $offer->flowRemote ?: null,
            'salary'     => $salary,
        ];
    }
}
