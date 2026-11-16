<?php

declare(strict_types=1);

namespace ErdmannFreunde\SeekerThemeBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Inhaltselement „Badge": kurze Hinweis- oder Kennzahl-Karte aus festen
 * Feldern (Überschrift, Wert, Hinweis). Feste Felder statt Rich Text, damit
 * die Karte immer dasselbe Markup bekommt. Das Template liest die Felder
 * aus `data`.
 */
#[AsContentElement(category: 'texts')]
class BadgeController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        return $template->getResponse();
    }
}
