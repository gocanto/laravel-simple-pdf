<?php

/*
 * This file is part of the Laravel-Simple-PDF package
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gocanto\SimplePDF\Tests;

use Dompdf\Dompdf;
use Dompdf\Options;
use Gocanto\SimplePDF\ExporterInterface;
use Gocanto\SimplePDF\Pdf;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class PdfTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_comes_with_default_options()
    {
        $pdf = new Pdf;
        $options = $pdf->getWriter()->getOptions();

        $this->assertInstanceOf(ExporterInterface::class, $pdf);
        $this->assertInstanceOf(Dompdf::class, $pdf->getWriter());

        $this->assertTrue(strpos($options->get('fontCache'), 'resources/views/templates/temp') !== false);
        $this->assertTrue($options->get('isRemoteEnabled'));
        $this->assertTrue($options->get('isHtml5ParserEnabled'));
        $this->assertTrue($options->get('isFontSubsettingEnabled'));
        $this->assertEquals($options->get('defaultMediaType'), 'print');
        $this->assertSame($options->get('dpi'), 120);
        $this->assertSame($options->get('fontHeightRatio'), 0.9);
    }

    /** @test */
    public function it_allows_writers_on_runtime()
    {
        $writer = Mockery::mock(Dompdf::class);

        $pdf = new Pdf;
        $pdf->setWriter($writer);

        $this->assertSame($writer, $pdf->getWriter());
    }

    /** @test */
    public function it_allows_custom_options()
    {
        $options = new Options;
        $options->setDpi(1);

        $writer = Mockery::mock(Dompdf::class);

        $writer->shouldReceive('getOptions')->once()->andReturn($options);
        $writer->shouldReceive('setOptions')->once()->with(Mockery::on(function ($args) use ($options) {
            return $args === $options;
        }));

        $pdf = new Pdf($writer, $options);
        $this->assertSame($pdf->getWriter()->getOptions()->get('dpi'), 1);
    }

    /** @test */
    public function it_allows_for_given_contents()
    {
        $pdf = new Pdf;

        $pdf->addContent('foo');
        $pdf->addContent('bar');

        $this->assertContains('foo', $pdf->getTemplates());
        $this->assertContains('bar', $pdf->getTemplates());
        $this->assertNotContains('biz', $pdf->getTemplates());
    }

    /** @test */
    public function it_exports_the_right_stream()
    {
        $writer = Mockery::mock(Dompdf::class);
        $writer->shouldReceive('setOptions')->once();
        $writer->shouldReceive('loadHtml')->once()->with('foo<div class="page_break"></div>', 'UTF-8');
        $writer->shouldReceive('render')->once();
        $writer->shouldReceive('output')->once()->andReturn('bar');

        $stream = Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('write')->once()->with('bar');

        $pdf = new Pdf($writer);
        $pdf->addContent('foo');

        $pdf->export($stream);
    }
}
