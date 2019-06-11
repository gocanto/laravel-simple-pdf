<?php

namespace Gocanto\SimplePDF;

use GuzzleHttp\Psr7\Stream;
use Illuminate\Contracts\View\Factory as ViewContract;
use Illuminate\View\Factory as ViewFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Builder
{
    /** @var ExporterInterface */
    private $exporter;
    /** @var ViewContract|ViewFactory */
    private $render;
    /** @var string */
    private $template = 'default';
    /** @var Stream */
    private $stream;
    /** @var array */
    private $headers = [
        'Content-type' => 'application/pdf',
    ];

    /**
     * @param ExporterInterface $exporter
     * @param ViewContract|ViewFactory $render
     */
    public function __construct(ExporterInterface $exporter, ViewContract $render)
    {
        $this->exporter = $exporter;
        $this->render = $render;
        $this->addLocation(__DIR__ . '/../resources/views/templates');
    }

    /**
     * @param string $location
     */
    public function addLocation(string $location) : void
    {
        $this->render->addLocation($location);
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template) : void
    {
        $this->template = $template;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers) : void
    {
        array_merge($this->headers, $headers);
    }

    /**
     * @param array $data
     */
    public function make(array $data) : void
    {
        $content = $this->render->make($this->getTemplate(), [
            'data' => $data,
        ]);

        $this->exporter->addContent($content);

        $this->stream = new Stream(fopen('php://temp', 'wb+'));

        $this->exporter->export($this->stream);
        $this->stream->rewind();
    }

    /**
     * @param callable|null $response
     * @return StreamedResponse
     */
    public function render(callable $response = null) : StreamedResponse
    {
        $response = $response !== null
            ? $response($this->stream)
            : function () {
                echo $this->stream->getContents();
            };

        return new StreamedResponse($response, StreamedResponse::HTTP_OK, $this->getHeaders());
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}

