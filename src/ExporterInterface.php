<?php

/*
 * This file is part of the Gocanto Laravel-Simple-PDF package
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gocanto\SimplePDF;

use Psr\Http\Message\StreamInterface;

interface ExporterInterface
{
    /**
     * @param string $content
     * @return mixed
     */
    public function addContent(string $content);

    /**
     * @param StreamInterface $stream
     * @return mixed
     */
    public function export(StreamInterface $stream);
}
