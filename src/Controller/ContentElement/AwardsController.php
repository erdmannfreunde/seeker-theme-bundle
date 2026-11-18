<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Inhaltselement „Auszeichnungen": Überschrift und Auszeichnungen als
 * Zeilen aus Icon + Text. Ersetzt die frühere Konstruktion aus Reihe,
 * Spalte und einzelnen Text-Elementen.
 */
#[AsContentElement(category: 'texts')]
class AwardsController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        // Zeilen ohne Text überspringen (leere Zeile aus dem rowWizard)
        $template->set('items', array_values(array_filter(
            StringUtil::deserialize($model->awards_items, true),
            static fn ($item): bool => \is_array($item) && '' !== trim((string) ($item['text'] ?? '')),
        )));

        return $template->getResponse();
    }
}
