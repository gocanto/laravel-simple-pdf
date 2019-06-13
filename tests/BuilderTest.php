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

use Gocanto\SimplePDF\Builder;
use Gocanto\SimplePDF\ExporterInterface;
use Gocanto\SimplePDF\TemplateContext;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Contracts\View\Factory;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BuilderTest extends TestCase
{
    /** @var ExporterInterface */
    private $exporter;
    /** @var Factory */
    private $renderer;
    /** @var Builder */
    private $builder;

    protected function setUp() : void
    {
        $this->exporter = Mockery::mock(ExporterInterface::class);
        $this->renderer = $this->getRendererMock();

        $this->builder = new Builder($this->exporter, $this->renderer);
    }

    protected function tearDown() : void
    {
        Mockery::close();
    }

    /** @test */
    public function it_handles_templates()
    {
        $this->assertEquals('default', $this->builder->getTemplate());

        $builder = $this->builder->withTemplate('foo');

        $this->assertEquals('foo', $builder->getTemplate());
        $this->assertEquals('default', $this->builder->getTemplate());
    }

    /** @test */
    public function it_handles_headers()
    {
        $this->assertEquals(['Content-type' => 'application/pdf'], $this->builder->getHeaders());

        $builder = $this->builder->withHeaders(['foo' => 'bar']);

        $this->assertEquals(['Content-type' => 'application/pdf', 'foo' => 'bar'], $builder->getHeaders());
        $this->assertEquals(['Content-type' => 'application/pdf'], $this->builder->getHeaders());
    }

    /** @test */
    public function it_handles_streams()
    {
        $this->assertNull($this->builder->getStream());

        $builder = $this->builder->withStream($stream = Mockery::mock(StreamInterface::class));

        $this->assertSame($stream, $builder->getStream());
        $this->assertNull($this->builder->getStream());
    }

    /** @test */
    public function it_creates_and_renders_the_proper_pdf_file()
    {
        $data = TemplateContext::make(['foo' => 'bar']);

        $stream = Mockery::mock(Stream::class);
        $stream->shouldReceive('rewind')->once();
        $stream->shouldReceive('getContents')->once()->andReturn('content');
        $stream->shouldReceive('fooBar')->once()->andReturn('content');

        $builder = $this->builder->withStream($stream);

        $this->renderer->shouldReceive('make')->once()->with('default', ['context' => $data])->andReturn('content');
        $this->exporter->shouldReceive('addContent')->once()->with('content');
        $this->exporter->shouldReceive('export')->once()->with($stream);

        $builder->make($data);

        $response = $builder->render();

        //custom callback functionality.
        $builder->render(function (StreamInterface $stream) {
            $this->assertNotNull($stream);
            $stream->fooBar();
        });

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame($response->getStatusCode(), StreamedResponse::HTTP_OK);
        $this->assertSame($response->headers->get('content-type'), 'application/pdf');

        $response->sendContent();
    }

    /** @test */
    public function it_does_not_allow_rendering_for_invalid_streams()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->builder->render();
    }

    private function getRendererMock()
    {
        $renderer = Mockery::mock(Factory::class);

        $renderer->shouldReceive('addLocation')->with(Mockery::on(function ($args) {
            return strpos($args, 'resources/views/templates') !== false;
        }));

        return $renderer;
    }
}
