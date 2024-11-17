<?php

namespace Kanuni\FilamentCards\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Collection;
use Kanuni\FilamentCards\Enums\Alignment;

abstract class CardsPage extends Page
{
    protected static string $view = 'filament-cards::pages.cards-page';

    protected function getViewData(): array
    {
        return [
            'cards' => $this->getItems(),
            'alignment' => $this->getItemsAlignment(),
            'isIconInlined' => $this->isIconInlined(),
            'iconSize' => $this->getIconSize(),
            'alignmentEnum' => Alignment::class,
            'canBeCollapsed' => fn (string $group): bool => $this->canBeCollapsed($group),
            'isCollapsed' => fn (string $group): bool => $this->isCollapsed($group),
        ];
    }

    protected static Alignment $itemsAlignment = Alignment::Center;

    protected static bool $iconInlined = false;

    protected static IconSize $iconSize = IconSize::Medium;

    // Specify groups that are initialy collapsed
    protected static array $collapsedGroups = [];

    // Specify groups that should not be collapsible, or disable collapsing for all groups.
    protected static bool|array $disableGroupsCollapse = false;

    protected static function getCards(): array
    {
        return [];
    }

    public function isIconInlined(): bool
    {
        return static::$iconInlined;
    }

    public function getIconSize(): IconSize
    {
        return static::$iconSize;
    }

    public function getItemsAlignment(): Alignment
    {
        return static::$itemsAlignment;
    }

    public function getItems(): Collection
    {
        $cards = $this->getCards();

        return collect($cards)
            ->each(fn ($item) => $item->originPage($this::class))
            ->groupBy(fn ($item) => $item->getGroup());
    }

    public function canBeCollapsed(string $groupName): bool
    {
        if (is_array(static::$disableGroupsCollapse)) {
            return in_array($groupName, static::$disableGroupsCollapse);
        }

        return ! static::$disableGroupsCollapse;
    }

    public function getCollapsedGroups(): array
    {
        return static::$collapsedGroups;
    }

    public function isCollapsed(string $group): bool
    {
        return in_array($group, $this->getCollapsedGroups());
    }

    public static function getPageGroup(string $page): ?string
    {
        $item = collect(static::getCards())
            ->first(fn ($item) => $item->getPage() === $page);

        if ($item) return $item->getGroup();

        return null;
    }
}
