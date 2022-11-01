<?php

namespace Otomaties\OtomatiesWordpressIframemanager;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @subpackage OtomatiesWordpressIframemanager/public
 */

class Frontend
{
    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $pluginName       The name of the plugin.
     */
    public function __construct(private string $pluginName)
    {
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     */
    public function enqueueStyles() : void
    {
        wp_enqueue_style($this->pluginName, Assets::find('css/main.css'), [], null);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     */
    public function enqueueScripts() : void
    {
        wp_enqueue_script($this->pluginName, Assets::find('js/main.js'), [], null, true);
        wp_localize_script($this->pluginName, 'props', [
            'l10n_notice' => sprintf(
                __('This content is hosted by a third party. By showing the external content you accept the %1$s of %2$s.', 'otomaties-wordpress-iframemanager'), // phpcs:ignore Generic.Files.LineLength.TooLong
                sprintf(
                    '<a rel="noreferrer" href="{{serviceUrl}}" title="Terms and conditions" target="_blank">%s</a>',
                    __('Terms and conditions', 'otomaties-wordpress-iframemanager')
                ),
                '{{serviceName}}'
            ),
            'l10n_loadVideo' => __('Load video', 'otomaties-wordpress-iframemanager'),
            'l10n_loadMap' => __('Load map', 'otomaties-wordpress-iframemanager'),
            'l10n_loadAllBtn' => __("Don't ask again", 'otomaties-wordpress-iframemanager'),
            'googleMaps' => [
                'thumbnailUrl' => Assets::find('images/map-placeholder.jpg'),
            ],
        ]);
    }

    private function parseServiceId(string $service, string $url) : ?string
    {
        if ($service === 'youtube') {
            $expression = '#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+|(?<=youtube.com/embed/|youtube-nocookie.com/embed/)[^?&\n]+#'; // phpcs:ignore Generic.Files.LineLength.TooLong
            preg_match($expression, $url, $matches);
            if (isset($matches[0])) {
                return $matches[0];
            }
        } elseif ($service === 'vimeo') {
            $expression = '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\#?)(?:[?]?.*)$%im'; // phpcs:ignore Generic.Files.LineLength.TooLong
            if (preg_match($expression, $url, $matches)) {
                return $matches[3];
            }
        } elseif ($service === 'googleMaps') {
            $parts = parse_url($url);
            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                return $query['pb'];
            }
        }
        return null;
    }

    /**
     * Undocumented function
     *
     * @param string $blockContent
     * @param array<string, mixed> $block
     * @return string|false
     */
    public function replaceIframes(string $blockContent, array $block) : string|false
    {
        $blockName = $block['blockName'];
        if ('core/embed' == $blockName) {
            if (!isset($block['attrs']['providerNameSlug'])) {
                return $blockContent;
            }
            $provider = $block['attrs']['providerNameSlug'];
    
            if (in_array($provider, ['youtube', 'vimeo'], true)) {
                $block = new DomElement($blockContent);
                $block->setService($provider);
                $block->setId(function ($iframe, $domElement) {
                    return $this->parseServiceId($domElement->getService(), $iframe->getAttribute('src'));
                });
                $blockContent = $block->replaceIframes();
            }
        } elseif ('core/html' == $blockName) {
            $block = new DomElement($blockContent);
    
            if (strpos($blockContent, 'google.com/maps/embed') !== false && strpos($blockContent, 'pb=') !== false) {
                $block->setService('googleMaps');
                $block->setTitle(function () use ($block) {
                    return apply_filters(
                        'otomaties_iframemanager_iframe_title',
                        __('Google maps', 'otomaties-wordpress-iframemanager'),
                        $block
                    );
                });
                $block->setId(function ($iframe) {
                    return $this->parseServiceId('googleMaps', $iframe->getAttribute('src'));
                });
                $blockContent = $block->replaceIframes();
            } elseif (strpos($blockContent, 'youtube.com/embed') !== false) {
                $block->setService('youtube');
                $block->setId(function ($iframe, $domElement) {
                    return $this->parseServiceId($domElement->getService(), $iframe->getAttribute('src'));
                });
                $blockContent = $block->replaceIframes();
            } elseif (strpos($blockContent, 'player.vimeo.com/video') !== false) {
                $block->setService('vimeo');
                $block->setTitle(function () use ($block) {
                    return apply_filters(
                        'otomaties_iframemanager_iframe_title',
                        __('Vimeo video player', 'otomaties-wordpress-iframemanager'),
                        $block
                    );
                });
                $block->setId(function ($iframe, $domElement) {
                    return $this->parseServiceId($domElement->getService(), $iframe->getAttribute('src'));
                });
                $blockContent = $block->replaceIframes();
            }
        }
    
        return $blockContent;
    }
}
