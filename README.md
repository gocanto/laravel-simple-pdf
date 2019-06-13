## About it

<a href="https://packagist.org/packages/gocanto/laravel-simple-pdf"><img src="https://img.shields.io/packagist/dt/gocanto/laravel-simple-pdf.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/gocanto/laravel-simple-pdf"><img src="https://img.shields.io/github/release/gocanto/laravel-simple-pdf.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://travis-ci.org/gocanto/laravel-simple-pdf"><img src="https://img.shields.io/travis/gocanto/laravel-simple-pdf/master.svg?style=flat-square" alt="Build status"></a>

This library is a minimalist on demand and `immutable PDF generator` for Laravel. It aims to keep a small friction once you need to generate a printable file using a intuitive public API.

Simple PDF is shipped with a default template that you will be able to use to frame your next PDF files. You can see its layout [here](https://github.com/gocanto/laravel-simple-pdf/blob/master/resources/views/templates/default.blade.php)

## Installation

This library uses [Composer](https://getcomposer.org) to manage its dependencies. So, before using it, make sure you have it installed in your machine. 
Once you have done this, you will be able to pull this library in by typing the following command in your terminal.

```
composer require gocanto/laravel-simple-pdf
```

## Default template implementation

Using the default template is the easiest way to get started. Like so:

```php
use Gocanto\SimplePDF\Builder;
use Gocanto\SimplePDF\TemplateContext;

Route::get('default-template', function (Builder $builder) {

    $context = TemplateContext::make([
        'title' => 'foo',
        'name' => 'bar',
        'content' => '<h1>Some amazing content!</h1>',
    ]);

    $builder->make($context);

    return $builder->render();
});
```

***What's going on here ?***

- First of all, we imported the `Builder` and the `Template Context` objects; you can think about them as the manager to generate your PDF files. For instance, the `Builder` 
is the one on charge of creating the `Stream File` we will be rending on demand while the another one holds the data to be display within the default template.

- Second of all,  we created the `TemplateContext` object with the desired data to be shown in our PDF file. The context object is a simple value object that holds some handy methods 
to manipulate the given array within our blade files. [See more](https://github.com/gocanto/laravel-simple-pdf/blob/master/src/TemplateContext.php)  

- Lastly, we invoke the `make` method passing in our context object and finish by returning with the `render` functionality. 
