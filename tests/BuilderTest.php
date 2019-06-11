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
use Illuminate\Contracts\View\Factory;
use Mockery;
use PHPUnit\Framework\TestCase;

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
        $this->renderer = Mockery::mock(Factory::class);
        $this->renderer->shouldReceive('addLocation');
        $this->builder = new Builder($this->exporter, $this->renderer);
    }

    protected function tearDown() : void
    {
        Mockery::close();
    }

    /** @test */
    public function it_makes_the_proper_voucher()
    {
        $this->renderer->shouldReceive('make')->once()->andReturn('content');
        $this->exporter->shouldReceive('addContent')->once();
        $this->exporter->shouldReceive('export')->once();

        $this->builder->make(['foo' => 'bar']);
    }
}
