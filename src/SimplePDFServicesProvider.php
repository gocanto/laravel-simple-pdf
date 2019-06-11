<?php

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
