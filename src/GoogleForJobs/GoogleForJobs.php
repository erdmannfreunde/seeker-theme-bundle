<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\GoogleForJobs;

use Contao\StringUtil;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\GoogleForJobs\GoogleForJobs as PlentaGoogleForJobs;

/**
 * Contao speichert Titel mit Input-Encoding, „(m/w/d)“ steht in der Datenbank
 * also als „&#40;m/w/d&#41;“. Plenta stellt im Google-for-Jobs-Markup nur die
 * Basis-Entities wieder her, die übrigen landen wörtlich im JSON-LD. Diese
 * Klasse ersetzt Plentas Service (config/services.yaml, decorates) und
 * dekodiert den Titel vorher vollständig.
 */
class GoogleForJobs extends PlentaGoogleForJobs
{
    public function generateStructuredData(PlentaJobsBasicOfferModel|null $jobOffer): string|null
    {
        if (null === $jobOffer) {
            return parent::generateStructuredData($jobOffer);
        }

        $title = $jobOffer->title;
        $jobOffer->title = StringUtil::decodeEntities((string) $title);

        try {
            return parent::generateStructuredData($jobOffer);
        } finally {
            $jobOffer->title = $title;
        }
    }
}
