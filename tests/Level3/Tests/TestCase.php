<?php

namespace Level3\Tests;

use Level3\Mocks\Mapper;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    const RELEVANT_KEY = 'Y';
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_HREF = 'XX';

    protected function createLevel3Mock()
    {
        return m::mock('Level3\Level3');
    }

    protected function createMapperMock()
    {
        return m::mock('Level3\Mapper');
    }

    protected function createHubMock()
    {
        return m::mock('Level3\Hub');
    }

    protected function createLinkMock()
    {
        return m::mock('Level3\Resource\Link');
    }

    protected function createFinderMock()
    {
        return m::mock('Level3\Repository,Level3\Repository\Finder');
    }

    protected function createGetterMock()
    {
        return m::mock('Level3\Repository,Level3\Repository\Getter');
    }

    protected function createDeleterMock()
    {
        return m::mock('Level3\Repository,Level3\Repository\Deleter');
    }

    protected function createPutterMock()
    {
        return m::mock('Level3\Repository,Level3\Repository\Putter');
    }

    protected function createPosterMock()
    {
        return m::mock('Level3\Repository,Level3\Repository\Poster');
    }

    protected function createPatcherMock()
    {
        return m::mock('Level3\Repository,Level3\Repository\Patcher');
    }

    protected function createRepositoryMock()
    {
        return m::mock('Level3\Repository');
    }

    protected function createMethodMock()
    {
        return m::mock('Level3\Processor\Wrapper\Authenticator\Method');
    }

    protected function createResourceMock($mockMethods = true)
    {
        $resource = m::mock('Level3\Resource\Resource');

        if ($mockMethods) {
            $resource->shouldReceive('getCache')->andReturn(null);
            $resource->shouldReceive('getId')->andReturn(null);
            $resource->shouldReceive('getLastUpdate')->andReturn(null);
        }

        return $resource;
    }

    protected function createProcessorMock()
    {
        return m::mock('Level3\Processor');
    }

    protected function createFormatWriterMock()
    {
        $formatter = m::mock('Level3\Resource\Format\Writer');
        $formatter->shouldReceive('getContentType')->andReturn('foo/bar');

        return $formatter;
    }

    protected function createParameterBagMock()
    {
        $parameters = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $parameters->shouldReceive('getIterator');

        return $parameters;
    }

    protected function createWrapperMock()
    {
        return m::mock('Level3\Processor\Wrapper\ExceptionHandler');
    }

    protected function createRequestMockSimple()
    {
        $request = m::mock('Level3\Messages\Request');
        $request->headers = $this->createParameterBagMock();
        $request->attributes = $this->createParameterBagMock();

        return $request;
    }

    protected function createResponseMock()
    {
        $response = m::mock('Level3\Messages\Response');
        $response->headers = $this->createParameterBagMock();

        return $response;
    }

    protected function createRequestMock()
    {
        $request = $this->createRequestMockSimple();

        return $request;
    }
}
