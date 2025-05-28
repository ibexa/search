<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Search\Twig\Extension;

use Ibexa\Contracts\Search\Event\UrlGeneratorEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlGeneratorExtension extends AbstractExtension
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('ibexa_generate_url', [$this, 'generateUrl']),
        ];
    }

    public function generateUrl($content): string
    {
        $event = $this->eventDispatcher->dispatch(
            new UrlGeneratorEvent($content)
        );

        return $event->getUrl();
    }
}
