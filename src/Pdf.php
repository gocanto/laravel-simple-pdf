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

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Pdf implements ExporterInterface
{
    /** @var Dompdf|null */
    protected $writer;
    /** @var array */
    protected $templates = [];

    /**
     * @param Dompdf|null $writer
     * @param Options|null $options
     */
    public function __construct(Dompdf $writer = null, Options $options = null)
    {
        $this->writer = $writer !== null ? $writer : $this->getDefaultWriter();

        $this->writer->setOptions(
            $options !== null ? $options : $this->getDefaultOptions()
        );
    }

    /**
     * @return Dompdf
     */
    private function getDefaultWriter() : Dompdf
    {
        $defaultWriter = new Dompdf;
        $defaultWriter->setPaper('A4', 'portrait');

        return $defaultWriter;
    }

    /**
     * @return Options
     */
    private function getDefaultOptions(): Options
    {
        $options = new Options;

        $options->setFontCache($this->getTempPath());
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsFontSubsettingEnabled(true);
        $options->setDefaultMediaType('print');
        $options->setIsFontSubsettingEnabled(true);
        $options->setDpi(120);
        $options->setFontHeightRatio(0.9);

        return $options;
    }

    /**
     * @return string
     */
    private function getTempPath() : string
    {
        $path = __DIR__ . '/../resources/views/templates/temp';

        if (!is_dir($path)) {
            mkdir($path, 0755, false);
        }

        return $path;
    }

    /**
     * @param string $content
     * @return void
     */
    public function addContent(string $content) : void
    {
        $this->templates[] = $content;
    }

    /**
     * @param StreamInterface $stream
     * @return mixed
     * @throws RuntimeException
     */
    public function export(StreamInterface $stream)
    {
        $content = '';

        foreach ($this->templates as $template) {
            $content = $this->addTemplateContent($content, $template);
            $content = $this->addPageBreak($content);
        }

        $this->getWriter()->loadHtml($content, 'UTF-8');
        $this->getWriter()->render();

        return $stream->write($this->getWriter()->output());
    }

    /**
     * @param string $content
     * @return string
     */
    private function addPageBreak(string $content): string
    {
        return $content . '<div class="page_break"></div>';
    }

    /**
     * @param string $content
     * @param string $voucher
     * @return string
     */
    private function addTemplateContent(string $content, string $voucher): string
    {
        return $content . $voucher;
    }

    /**
     * @return Dompdf
     */
    public function getWriter(): Dompdf
    {
        return $this->writer !== null ? $this->writer : $this->getDefaultWriter();
    }

    /**
     * @param Dompdf $writer
     */
    public function setWriter(Dompdf $writer): void
    {
        $this->writer = $writer;
    }
}
