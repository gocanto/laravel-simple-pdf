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
use Gocanto\SimplePDF\Pdf;
use Gocanto\SimplePDF\SimplePDFServicesProvider;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Mockery;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_register_the_right_builder_within_the_container()
    {
        $singletonCallback = function ($register) {
            $builder = $register();

            return $builder instanceof Builder;
        };

        $factory = Mockery::mock(Factory::class);
        $factory->shouldReceive('addLocation')->once();

        $app = $this->getAppMock();

        $app->shouldReceive('singleton')->once()->with(Builder::class, Mockery::on($singletonCallback));
        $app->shouldReceive('make')->once()->with(Pdf::class)->andReturn(Mockery::mock(Pdf::class));
        $app->shouldReceive('make')->once()->with(Factory::class)->andReturn($factory);

        $app->shouldReceive('bind')->once()->with(ExporterInterface::class, Pdf::class);
        $app->shouldReceive('bind')->once()->with('simple.pdf.writer', ExporterInterface::class);
        $app->shouldReceive('bind')->once()->with('simple.pdf.builder', Builder::class);

        $provider = new SimplePDFServicesProvider($app);
        $provider->register();

        $this->assertContains(Builder::class, $provider->provides());
        $this->assertCount(1, $provider->provides());
    }

    public function getAppMock()
    {
        $view = Mockery::mock(Factory::class);
        $view->shouldReceive('addNamespace')->once();

        $app = Mockery::mock(Container::class);
        $app->shouldReceive('offsetGet')->with('view')->once()->andReturn($view);
        $app->shouldReceive('offsetGet')->with('config')->once()->andReturn([
            'view' => [
                'paths' => 'path',
            ],
        ]);

        /** @var Application $app */
        return $app;
    }
}
