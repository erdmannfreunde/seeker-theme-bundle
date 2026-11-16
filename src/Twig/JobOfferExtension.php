<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\Twig;

use ErdmannFreunde\SeekerThemeBundle\Helper\JobInfoHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig-Funktionen für die Stellen-Templates.
 *
 *   seeker_job_salary(jobOffer)  formatiertes Gehalt wie im Plenta-Reader
 */
class JobOfferExtension extends AbstractExtension
{
    public function __construct(
        private readonly JobInfoHelper $jobInfoHelper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seeker_job_salary', $this->jobInfoHelper->formatSalary(...)),
        ];
    }
}
