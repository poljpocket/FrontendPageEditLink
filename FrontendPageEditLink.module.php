<?php namespace ProcessWire;

class FrontendPageEditLink extends Wire implements Module
{
    /**
     * Basic information about module
     */
    public static function getModuleInfo(): array
    {
        return [
            'title' => __('Frontend Page Edit Links'),
            'summary' => __('Adds a floating button with a link to the page editor to every editable page in the frontend.'),
            'author' => 'Julian Pollak',
            'url' => 'https://github.com/poljpocket/FrontendPageEditLink',
            'version' => 100,
            'autoload' => true,
            'singular' => true,
            'requires' => [
                'ProcessWire>=3.0.200',
                'PHP>=8.1'
            ],
            'icon' => 'pencil',
        ];
    }

    public function init(): void {
        $this->addHookAfter('Page::render', $this->addEditLinkHtml(...));
    }

    public function addEditLinkHtml(HookEvent $event): void {
        /** @var Page $page */
        $page = $event->object;

        if ($page->rootParent->id === 2) return;
        if (!$page->editable()) return;

        /** @var string $html */
        $html = $event->return;

        if (!strpos($html, '</body>')) return;

        $config = $event->config;
        $sanitizer = $event->sanitizer;
        $files = $event->files;

        $newMarkup = $files->fileGetContents($config->paths($this) . 'templates/edit-link-markup.html');

        $newMarkup = $sanitizer->getTextTools()->populatePlaceholders($newMarkup, [
            'url' => $page->editUrl,
        ]);

        $event->return = str_replace('</body>', $newMarkup . '</body>', $html);
    }
}
