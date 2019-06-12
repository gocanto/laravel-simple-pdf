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

use GuzzleHttp\Psr7\Stream;
use Illuminate\Contracts\View\Factory as ViewContract;
use Illuminate\View\Factory as ViewFactory;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Builder
{
    /** @var ExporterInterface */
    private $exporter;
    /** @var ViewContract|ViewFactory */
    private $render;
    /** @var string */
    private $template = 'default';
    /** @var StreamInterface */
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
     * @param array $data
     */
    public function make(array $data) : void
    {
        $content = $this->render->make($this->getTemplate(), [
            'data' => $data,
        ]);

        $this->exporter->addContent($content);

        $stream = $this->getStream();

        $this->exporter->export($stream);
        $stream->rewind();
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
     * @param StreamInterface $stream
     * @return Builder
     */
    public function withStream(StreamInterface $stream): Builder
    {
        $builder = clone $this;

        $builder->stream = $stream;

        return $builder;
    }

    /**
     * @param string $template
     * @return Builder
     */
    public function withTemplate(string $template): Builder
    {
        $builder = clone $this;

        $builder->template = $template;

        return $builder;
    }

    /**
     * @param array $headers
     * @return Builder
     */
    public function withHeaders(array $headers): Builder
    {
        $builder = clone $this;

        $builder->headers = array_merge($builder->headers, $headers);

        return $builder;
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

    /**
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        return $this->stream === null
            ? new Stream(fopen('php://temp', 'wb+'))
            : $this->stream;
    }
}

