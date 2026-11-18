<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Contao\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Inhaltselement „Standort": Bild, Dachzeile, Überschrift,
 * Beschreibung, Fakten mit Icon und zwei Links (Stellen am Standort,
 * Adresse & Anfahrt) in einem Element. Ersetzt die frühere Konstruktion
 * aus drei verschachtelten Elementgruppen.
 */
#[AsContentElement(category: 'texts')]
class LocationController extends AbstractContentElementController
{
    public function __construct(
        private readonly Studio $studio,
        private readonly InsertTagParser $insertTagParser,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('text', $model->text ?: '');

        $template->set('image', $this->studio
            ->createFigureBuilder()
            ->fromUuid($model->singleSRC ?: '')
            ->setSize($model->size)
            ->buildIfResourceExists());

        // Zeilen ohne Text überspringen (leere Zeile aus dem rowWizard)
        $template->set('facts', array_values(array_filter(
            StringUtil::deserialize($model->location_facts, true),
            static fn ($fact): bool => \is_array($fact) && '' !== trim((string) ($fact['text'] ?? '')),
        )));

        $template->set('jobs_href', $this->href($model->location_jobs_url, $request));
        $template->set('map_href', $this->href($model->location_map_url, $request));

        return $template->getResponse();
    }

    // Wie beim Hyperlink-Element: Insert-Tags auflösen, relative URLs mit Basispfad
    private function href(?string $url, Request $request): string
    {
        $href = $this->insertTagParser->replaceInline($url ?? '');

        if ('' !== $href && Validator::isRelativeUrl($href)) {
            $href = $request->getBasePath().'/'.$href;
        }

        return $href;
    }
}
