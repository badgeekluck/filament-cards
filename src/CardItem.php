<?php

namespace Kanuni\FilamentCards;

use Closure;
use Filament\Pages\Page;

class CardItem
{
    public static string $originQueryParameter = 'origin';

    protected ?string $originPage = null;

    public function __construct(
        protected ?string $page = null,
        protected ?string $title = null,
        protected ?string $description = null,
        protected ?string $icon = null,
        protected string|Closure|null $group = null,
        protected ?string $url = null,
        protected bool $openInNewTab = false,
    )
    {}

    public static function make(string $pageClassOrUrl)
    {
        return class_exists($pageClassOrUrl) && is_a($pageClassOrUrl, Page::class, true)
            ? new static(page: $pageClassOrUrl)
            : new static(url: $pageClassOrUrl);
    }

    public function originPage(string $page): static
    {
        $this->originPage = $page;

        return $this;
    }

    public function title(string|Closure $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(string|Closure $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string|Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function group(string|Closure|null $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function url(string|Closure $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function openInNewTab(bool $newTab = true): static
    {
        $this->openInNewTab = $newTab;

        return $this;
    }

    public function shouldOpenInNewTab(): bool
    {
        return $this->openInNewTab;
    }

    public function getTitle(): string
    {
        $title = ($this->title instanceof Closure)
            ? ($this->title)()
            : $this->title;

        if (blank($title) && isset($this->page)) {
            if ($resource = $this->getPageResource()) {
                $title = $resource::getNavigationLabel();
            } else {
                $title = $this->page::getNavigationLabel();
            }
        }

        return $title ?? '';
    }

    public function getDescription(): string
    {
        if ($this->description instanceof Closure) {
            return ($this->description)();
        }

        return $this->description ?? '';
    }

    public function getIcon(): ?string
    {
        $icon = ($this->icon instanceof Closure)
            ? ($this->icon)()
            : $this->icon;

        if (blank($icon) && isset($this->page)) {
            // First try to get page's icon
            $icon = $this->page::getNavigationIcon();

            if (blank($icon) && $resource = $this->getPageResource()) {
                // Try to get icon from page's resource class
                $icon = $resource::getNavigationIcon();
            }
        }

        return $icon;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function getGroup(): ?string
    {
        if ($this->group instanceof Closure) {
            return ($this->group)();
        }

        return $this->group;
    }

    public function getUrl(): string
    {
        $url = ($this->url instanceof Closure)
            ? ($this->url)()
            : $this->url;

        if (filled($url)) {
            return $url;
        }

        if (isset($this->page)) {
            $url = $this->page::getUrl();

            // Append origin parameter to the page link
            $paramName = static::$originQueryParameter;
            $paramValue = $this->getOriginParameter();

            return "$url?{$paramName}={$paramValue}";
        }

        return '#';
    }

    protected function getPageResource(): ?string
    {
        if (! isset($this->page)) return null;

        return method_exists($this->page, 'getResource')
            ? $this->page::getResource()
            : null;
    }

    protected function getOriginParameter(): ?string
    {
        return isset($this->originPage)
            ? urlencode(base64_encode($this->originPage))
            : null;
    }
}
