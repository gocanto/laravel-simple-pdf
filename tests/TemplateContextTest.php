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

use Gocanto\SimplePDF\TemplateContext;
use PHPUnit\Framework\TestCase;

class TemplateContextTest extends TestCase
{
    /** @test */
    public function it_handles_attributes()
    {
        $context = TemplateContext::make([
            'foo' => 'bar',
        ]);

        $this->assertTrue($context->has('foo'));
        $this->assertEquals('bar', $context->get('foo'));
    }
}
