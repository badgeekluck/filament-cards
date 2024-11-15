# Filament Settings Panel Plugin

The Filament Cards plugin enables you to create a page containing cards. Card items can be other Filament pages or custom item with a link and can be organized into logical groups for easier navigation, if you have a lots of cards. Each card can have title, an icon and description. When Filament page is added as an item, the cards page automatically applied the page's title, icon and URL, although you can customize these properties as needed.

The best use case for this cards page would be application control panel or settings hub, where you can organize all of the application settings on one page.

## Screenshot

![Default cards layout](./img/default-view.png "Default layout")

Default view of the Cards Page with items organized into groups and displayed as individual cards

## Installation

Install the plugin using Composer:

```bash
composer require kanuni/filament-cards
```

## Creating a cards page

For the sake of an example we are going to create application control panel page. Create filament page in `App\Filament\Pages\ControlPanel.php`. Insted of extending filament page class this page must extends `\Kanuni\FilamentCards\Filament\Pages\CardsPage` class.

Also we need to add private static method `getCards()` that will return an array of `CardItem` objects.

```php
namespace App\Filament\Pages;

use Filament\FilamentCards\Filament\Pages\CardsPage;
use Filament\FilamentCards\CardItem;
use App\Filament\Pages\CompanySettings;

class ControlPanel extends CardsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    private static function getCards(): array
    {
        return [
            CardItem::make(CompanySettings::class)
        ];
    }
}
```

In above example we added `CompanySettings` page as card item on our control panel page. The card item will inherit title and icon from that page. If you want to override that convention or add optional description you can use methods `title()`, `icon()` or `description()` on `CardItem` object.

Also note that if provided page to card item is part of Filament resource then title and icon will be used from resource class.

## Adding Custom Link as Card Item

You can add custom link as a card item by passing URL to CardItem's `make()` method. See following example:

```php
use Kanuni\FilamentCards\CardItem;

private static function getCards(): array
{
    return [
        CardItem::make('/path/to/docs')
            ->title('Documentation')
            ->icon('heroicon-o-document-text')
            ->description('Read the docs')
    ];
}
```

## Grouping Panel Items

Organize card items into collapsible groups by using the `group()` method on a `CardItem` object:

```php
use Kanuni\FilamentCards\CardItem;

private static function getCards(): array
{
    return [
        CardItem::make(CompanySettings::class)->group('General')
    ];
}
```

## Collapse Groups

By default, all groups on the card's page are expanded when you open the page. However, you can specify which groups should be collapsed initially. To do this, use the `collapsedGroups` property on the card's page instance and pass an array of group names to be collapsed.

```php
use Kanuni\FilamentCards\Filament\Page\CardsPage;

class ControlPanel extends CardsPage
{
    protected static array $collapsedGroups = ['General', 'Advanced'];

    private static function getCards(): array
    {
        return [...];
    }
}
```

In this example, the "General" and "Advanced" groups will be collapsed by default, allowing users to expand them only when needed. This feature helps keep the page organized, especially when there are multiple groups containing lots of items.

If you need to conditionaly collapse some of the groups you can override `getCollapsedGroups()` on your page and return list of groups that should be collapsed, for example:

```php
use Kanuni\FilamentCards\Filament\Page\CardsPage;

class ControlPanel extends CardsPage
{
    public function getCollapsedGroups(): array
    {
        if (auth()->user()->role !== 'admin') {
            // For non-admin users collapse 'Advanced' group
            return ['Advanced'];
        }

        return [];
    }
```

## Defining a Custom URL and/or Open Link in New Tab

By default, when your card item is a Filament page the card item uses that page's URL. However, you can specify a custom URL with the `url()` method on the `CardItem` object:

```php
use Filament\FilamentCards\CardItem;

private static function getCards(): array
{
    return [
        CardItem::make(CompanySettings::class)
            // Override page URL
            ->url('https://www.google.com')
            // Will open link in new tab
            ->openInNewTab()
    ];
}
```

You can also use absolute URL's like in the above example. Optionally you can change to open the link in new browser tab using `openInNewTab()` method.

## Customizing the Display of Card Items

By default, the content of each card item (title, icon, and description) is stacked and centered. Customize this alignment with the `itemsAlignment` property on the card's page. The property must be an enum value from `Kanuni\FilamentCards\Enums\Alignment`. Possible values are `Alignment::Start`, `Alignment::Center` and `Alignment::End`.

```php
use Kanuni\FilamentCards\Filament\Page\CardsPage;
use Kanuni\FilamentCards\Enums\Alignment;

class ControlPanel extends CardsPage
{
    // Change alignment of card's title, icon and description
    protected static Alignment $itemsAlignment = Alignment::Start;
}
```

![Card items aligned to start](./img/align-start.png "Alignment of card items")

### Changing the Icon Size

You can customize the icon size by overriding `iconSize` property on the card's page. This property must be  value from the `Filament\Support\Enums\IconSize` enum. There are three sizes `IconSize::Small`, `IconSize::Medium` and `IconSize::Large`. Default size is medium.

```php
use Kanuni\FilamentCards\Filament\Page\CardsPage;
use Filament\Support\Enums\IconSize;

class ControlPanel extends CardsPage
{
    // Change the size of card's icons
    protected static IconSize $iconSize = IconSize::Small;
}
```

### Inlining the Icon with the Card Title

To display an item's icon inline with its title, override the `iconInlined` property on the card's page.

```php
use Kanuni\FilamentCards\Filament\Page\CardsPage;
use Filament\Support\Enums\IconSize;

class ControlPanel extends CardsPage
{
    // Inline the card's icon with title
    protected static bool $iconInlined = true;
}
```

In this example, the icon is inlined with the title, and its size is set to small.

![Small icon inlined with title](./img/inlined-small-icon.png "Small icon inlined with title")

## Configuring Breadcrumbs for Pages Opened from the Cards Page

By default, pages opened from the cards page will display the standard breadcrumbs. If you want to customize the breadcrumbs for pages accessed through the cards page, you can add the `Kanuni\FilamentCards\Concerns\HasCardsBreadcrumb` trait in your page class.

```php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Kanuni\FilamentCards\Concerns\HasCardsBreadcrumb;

class CompanySettings extends Page
{
    use HasSettingsBreadcrumb;
}
```

When this trait is applied, the breadcrumbs will be set according to the card item, but only if the page is accessed from the card's page. This allows you to customize the navigation for card-related pages while keeping default behavior for other pages.
