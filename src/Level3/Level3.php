<?php

namespace Level3;

use Level3\Processor\Wrapper;
use Level3\Resource\Format\Writer;
use Symfony\Component\HttpFoundation\ParameterBag;

class Level3
{
    const PRIORITY_LOW = 10;
    const PRIORITY_NORMAL = 20;
    const PRIORITY_HIGH = 30;

    const CONTENT_TYPE_WILDCARD = '*/*';

    private $debug;
    private $hub;
    private $mapper;
    private $processor;
    private $wrappers = [];
    private $formatWriters = [];
    private $defaultContentType;

    public function __construct(Mapper $mapper, Hub $hub, Processor $processor)
    {
        $this->hub = $hub;
        $this->mapper = $mapper;
        $this->processor = $processor;

        $processor->setLevel3($this);
        $hub->setLevel3($this);
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function getHub()
    {
        return $this->hub;
    }

    public function getMapper()
    {
        return $this->mapper;
    }

    public function getProcessor()
    {
        return $this->processor;
    }

    public function getRepository($repositoryKey)
    {
        return $this->hub->get($repositoryKey);
    }

    public function getURI($repositoryKey, $interface = null, ParameterBag $attributes = null)
    {
        return $this->mapper->getURI($repositoryKey, $interface, $attributes);
    }

    public function clearProcessWrappers()
    {
        $this->wrappers = [];
    }

    public function addProcessorWrapper(Wrapper $wrapper, $priority = self::PRIORITY_NORMAL)
    {
        $this->wrappers[$priority][] = $wrapper;
        $wrapper->setLevel3($this);
    }

    public function getProcessorWrappers()
    {
        $result = [];

        ksort($this->wrappers);
        foreach ($this->wrappers as $priority => $wrappers) {
            $result = array_merge($result, $wrappers);
        }

        return $result;
    }

    public function getProcessorWrappersByClass($class)
    {
        foreach ($this->getProcessorWrappers() as $wrapper) {
            if ($wrapper instanceof $class) {
                return $wrapper;
            }
        }
    }

    public function addFormatWriter(Writer $writer)
    {
        $contentType = $writer->getContentType();
        if (!$this->defaultContentType) {
            $this->defaultContentType = $contentType;
        }

        $this->formatWriters[$contentType] = $writer;
    }

    public function getFormatWriter()
    {
        return $this->formatWriters;
    }

    public function getFormatWriterByContentType($contentType)
    {
        if ($contentType == self::CONTENT_TYPE_WILDCARD) {
            $contentType = $this->defaultContentType;
        }

        if (!isset($this->formatWriters[$contentType])) {
            return null;
        }

        return $this->formatWriters[$contentType];
    }

    public function setDefaultFormatWriter($contentType)
    {
        $this->defaultContentType = $contentType;
    }

    public function boot()
    {
        $this->mapper->boot($this->getHub());
    }
}
