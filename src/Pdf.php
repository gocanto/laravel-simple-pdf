<?php

namespace Gocanto\SimplePDF;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Pdf implements ExporterInterface
{
    /** @var Dompdf|null */
    protected $pdfWriter;
    /** @var array */
    protected $vouchers = [];
    /** @var string */
    protected $templateName;
    /** @var string */
    protected $templateTitle;
    /** @var ViewFactory */
    private $renderer;

    /**
     * @param Dompdf|null $pdfWriter
     * @param Options|null $pdfOptions
     * @param ViewFactory|null $renderer
     */
    public function __construct(Dompdf $pdfWriter = null, Options $pdfOptions = null, ViewFactory $renderer = null)
    {
        if ($pdfWriter === null) {
            $this->pdfWriter = new Dompdf;
            $this->pdfWriter->setPaper('A4', 'portrait');
        } else {
            $this->pdfWriter = $pdfWriter;
        }

        if ($pdfOptions === null) {
            $options = new Options();
            $options->setFontCache($this->getTempPath());
            $options->setIsRemoteEnabled(true);
            $options->setIsHtml5ParserEnabled(true);
            $options->setIsFontSubsettingEnabled(true);
            $options->setDefaultMediaType('print');
            $options->setIsFontSubsettingEnabled(true);
            $options->setDpi(120);
            $options->setFontHeightRatio(0.9);
            $this->pdfWriter->setOptions($options);
        } else {
            $this->pdfWriter->setOptions($pdfOptions);
        }

        $this->renderer = $renderer;
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
        $this->vouchers[] = $content;
    }

    /**
     * @param StreamInterface $stream
     * @return mixed
     * @throws RuntimeException
     */
    public function export(StreamInterface $stream)
    {
        $content = '';

        foreach ($this->vouchers as $voucher) {
            $content = $this->addVoucherContent($content, $voucher);
            $content = $this->addPageBreak($content);
        }

        if ($this->renderer !== null) {
            $view = $this->renderer->make('simplepdf::templates.default', [
                'pdfContent' => $content,
                'templateName' => $this->getTemplateName(),
                'templateTitle' => $this->getTemplateTitle(),
            ]);

            $content = $view->render();
        }

        $this->pdfWriter->loadHtml($content, 'UTF-8');
        $this->pdfWriter->render();

        return $stream->write($this->pdfWriter->output());
    }

    /**
     * @param string $content
     * @return string
     */
    public function addPageBreak(string $content): string
    {
        return $content . '<div class="page_break"></div>';
    }

    /**
     * @param string $content
     * @param string $voucher
     * @return string
     */
    protected function addVoucherContent(string $content, string $voucher): string
    {
        return $content . $voucher;
    }

    /**
     * @return Dompdf
     */
    public function getPdfWriter(): Dompdf
    {
        return $this->pdfWriter;
    }

    /**
     * @param ViewFactory $renderer
     */
    public function setRenderer(ViewFactory $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    /**
     * @param mixed $templateName
     */
    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    /**
     * @return string
     */
    public function getTemplateTitle(): string
    {
        return $this->templateTitle;
    }

    /**
     * @param string $templateTitle
     */
    public function setTemplateTitle(string $templateTitle): void
    {
        $this->templateTitle = $templateTitle;
    }
}
