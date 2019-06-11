<?php

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
