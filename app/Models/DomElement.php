<?php
namespace Otomaties\OtomatiesWordpressIframemanager\Models;

class DomElement
{

    private string $service = '';
    private $titleCallback;
    private $idCallback;
    private bool $autoscale = true;

    /**
     * Initialize domelement
     *
     * @param string $html
     * @param array<string, mixed> $block
     */
    public function __construct(private string $html, private array $block)
    {
        $this->titleCallback = function ($iframe, $domElement) {
            $title = $iframe->getAttribute('title') ?: __('External content', 'iframemanager');
            return apply_filters('otomaties_iframemanager_iframe_title', $title, $this);
        };

        $this->idCallback = function ($iframe, $domElement) {
            return '';
        };
    }

    /**
     * Set service for iframes inside this element
     *
     * @param string $service
     * @return void
     */
    public function setService(string $service) : void
    {
        $this->service = $service;
    }

    /**
     * Get service for iframes inside this element
     *
     * @return string
     */
    public function getService() : string
    {
        return $this->service;
    }

    /**
     * Set title for replacing div
     *
     * @param \Closure $callback
     * @return void
     */
    
    public function setTitle(\Closure $callback) : void
    {
        $this->titleCallback = $callback;
    }

    /**
     * Set iframe data-id
     *
     * @param \Closure $callback
     * @return void
     */
    public function setId(\Closure $callback) : void
    {
        $this->idCallback = $callback;
    }

    /**
     * Specify for responsive iframe
     *
     * @param boolean $autoscale
     * @return void
     */
    public function setAutoscale(bool $autoscale) : void
    {
        $this->autoscale = $autoscale;
    }

    /**
     * Get wordpress block
     *
     * @return array<string, mixed>
     */
    public function getBlock() : array
    {
        return $this->block;
    }

    /**
     * Get domm element html
     *
     * @return string
     */
    public function getHtml() : string
    {
        return $this->html;
    }

    /**
     * Replace iframes inside domelement with div
     *
     * @return false|string
     */
    public function replaceIframes() : false|string
    {
        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML(
            mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $iframes = $doc->getElementsByTagName('iframe');
        foreach ($iframes as $iframe) {
            $attributes = [
                'data-service' => $this->service,
                'data-id' => ($this->idCallback)($iframe, $this),
                'data-title' => ($this->titleCallback)($iframe, $this),
                'data-autoscale' => $this->autoscale,
            ];

            $im = $doc->createElement('div');

            foreach ($attributes as $name => $value) {
                $attribute = $doc->createAttribute($name);
                if ($value) {
                    $attribute->value = $value;
                }
                $im->appendChild($attribute);
            }

            if (property_exists($iframe, 'parentNode') && $iframe->parentNode) {
                $iframe->parentNode->appendChild($im);
                $iframe->parentNode->removeChild($iframe);
            }
        }

        return $doc->saveHTML();
    }
}
