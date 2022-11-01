<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DomElementTest extends TestCase
{
    public function testServiceCanBeSet(): void
    {
        $domElement = new Otomaties\OtomatiesWordpressIframemanager\DomElement('', []);
        $domElement->setService('youtube');
        $this->assertEquals('youtube', $domElement->getService());
    }
}
