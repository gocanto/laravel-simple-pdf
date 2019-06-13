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
use InvalidArgumentException;
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
     * @param TemplateContext $context
     */
    public function make(TemplateContext $context) : void
    {
        $content = $this->render->make($this->getTemplate(), [
            'context' => $context,
        ]);

        $this->exporter->addContent($content);

        $this->stream = $this->stream ?? new Stream(fopen('php://temp', 'wb+'));

        $this->exporter->export($this->stream);
        $this->stream->rewind();
    }

    /**
     * @param callable|null $response
     * @return StreamedResponse
     */
    public function render(callable $response = null) : StreamedResponse
    {
        if ($this->getStream() === null) {
            throw new InvalidArgumentException('The given stream is not valid. Forgot running the methods [make or withStream] method before?');
        }

        $response = $response !== null
            ? $response($this->getStream())
            : function () {
                echo $this->getStream()->getContents();
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
     * @return StreamInterface|null
     */
    public function getStream(): ?StreamInterface
    {
        return $this->stream;
    }
}
