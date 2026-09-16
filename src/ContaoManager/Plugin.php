<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use ErdmannFreunde\SeekerThemeBundle\SeekerThemeBundle;
use Nutshell\CardElement\NutshellCardElementBundle;
use Plenta\ContaoJobsBasic\PlentaContaoJobsBasicBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            // LoadAfter steuert sowohl DCA-Erweiterungs-Reihenfolge als auch
            // Template-Override-Priorität: wir ergänzen Felder am Plenta-Job
            // und überschreiben das Reader-Template — das funktioniert nur,
            // wenn unser Bundle NACH Plenta geladen wird.
            BundleConfig::create(SeekerThemeBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    NutshellCardElementBundle::class,
                    PlentaContaoJobsBasicBundle::class,
                ]),
        ];
    }
}
