<?php

/*
 * This file is part of the Laravel-Simple-PDF package
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gocanto\SimplePDF;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;

class SimplePDFServicesProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    public function register() : void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'simplepdf');

        $this->app->singleton(Builder::class, function () {
            return new Builder(
                $this->app->make(Pdf::class),
                $this->app->make(Factory::class)
            );
        });

        $this->app->bind(ExporterInterface::class, Pdf::class);
        $this->app->bind('simple.pdf.writer', ExporterInterface::class);
        $this->app->bind('simple.pdf.builder', Builder::class);
    }

    /**
     * @return array
     */
    public function provides() : array
    {
        return [
            Builder::class,
        ];
    }
}
