<?php

namespace Kanuni\FilamentCards\Concerns;

use Kanuni\FilamentCards\CardItem;

trait HasOriginBreadcrumb
{
    public function getBreadcrumbs(): array
    {
        $paramName = CardItem::$originQueryParameter;
        $origin = request()->query($paramName);
        $breadcrumbs = parent::getBreadcrumbs();

        if (! isset($origin)) {
            return $breadcrumbs;
        }

        $originCardsPage = base64_decode($origin);

        if (! class_exists($originCardsPage)) {
            return $breadcrumbs;
        }

        // Override original breadcrumbs
        $breadcrumbs = [$originCardsPage::getUrl() => $originCardsPage::getNavigationLabel()];

        $group = $originCardsPage::getPageGroup(static::class);

        if ($group) {
            $breadcrumbs[] = $group;
        }

        return $breadcrumbs;
    }
}
