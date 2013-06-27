<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Hal\Resource;
use Level3\Messages\Exceptions\AttributeNotFound;
use Level3\Messages\Processors\AccessorWrapper;
use Level3\Messages\RequestFactory;
use Level3\Repository\Exception\Conflict;
use Level3\Repository\Exception\DataError;
use Level3\Repository\Exception\NoContent;
use Level3\Repository\Exception\NotFound;
use Mockery as m;

class AccessorWrapperTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'XX';
    const IRRELEVANT_CONTENT = 'Y';
    const IRRELEVANT_RESPONSE = 'YY';

    private $accessorMock;
    private $messageProcessorMock;
    private $dummyRequest;
    private $dummyResource;

    private $accessorWrapper;

    public function setUp()
    {
        $this->accessorMock = m::mock('Level3\Accessor');
        $this->messageProcessorMock = m::mock('Level3\Messages\MessageProcessor');
        $this->dummyRequest = $this->createDummyRequest();
        $this->accessorWrapper = new AccessorWrapper($this->accessorMock, $this->messageProcessorMock);
        $this->dummyResource = new Resource();
    }

    private function createDummyRequest()
    {
        $requestFactory = new RequestFactory();
        return $requestFactory->clear()
            ->withId(self::IRRELEVANT_ID)
            ->withKey(self::IRRELEVANT_KEY)
            ->withContent(self::IRRELEVANT_CONTENT)
            ->create();
    }

    public function tearDown()
    {
        $this->accessorMock = null;
        $this->messageProcessorMock = null;
        $this->accessorWrapper = null;
        $this->dummyRequest = null;
        $this->dummyResource = null;
    }

    public function testFind()
    {
        $this->accessorMock->shouldReceive('find')->with(self::IRRELEVANT_KEY)->once()->andReturn($this->dummyResource);
        $this->messageProcessorMock->shouldReceive('createOKResponse')
            ->with($this->dummyRequest, $this->dummyResource)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->find($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider baseExceptions
     */
    public function testFindShouldFailWithBaseException($exception)
    {
        $this->accessorMock->shouldReceive('find')->with(self::IRRELEVANT_KEY)->once()->andThrow($exception);
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with($exception->getCode(), $exception->getMessage())->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->find($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testFindShouldFailWithAnyException()
    {
        $this->accessorMock->shouldReceive('find')->with(self::IRRELEVANT_KEY)->once()->andThrow('\Exception');
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with(500)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->find($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGet()
    {
        $this->accessorMock->shouldReceive('get')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()->andReturn($this->dummyResource);
        $this->messageProcessorMock->shouldReceive('createOKResponse')
            ->with($this->dummyRequest, $this->dummyResource)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->get($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider baseExceptions
     */
    public function testGetShouldFailWithBaseException($exception)
    {
        $this->accessorMock->shouldReceive('get')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()->andThrow($exception);
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with($exception->getCode(), $exception->getMessage())->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->get($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGetShouldFailWithAnyException()
    {
        $this->accessorMock->shouldReceive('get')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()->andThrow('\Exception');
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with(500)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->get($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPost()
    {
        $this->accessorMock->shouldReceive('post')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, array())->once()->andReturn($this->dummyResource);
        $this->messageProcessorMock->shouldReceive('createOKResponse')
            ->with($this->dummyRequest, $this->dummyResource)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());

        $response = $this->accessorWrapper->post($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider baseExceptions
     */
    public function testPostShouldFailWithBaseException($exception)
    {
        $this->accessorMock->shouldReceive('post')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, array())->once()->andThrow($exception);
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with($exception->getCode(), $exception->getMessage())->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());

        $response = $this->accessorWrapper->post($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPostShouldFailWithAnyException()
    {
        $this->accessorMock->shouldReceive('post')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, array())->once()->andThrow('\Exception');
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with(500)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->post($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPut()
    {
        $this->accessorMock->shouldReceive('put')->with(self::IRRELEVANT_KEY, array())->once()->andReturn($this->dummyResource);
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());
        $this->messageProcessorMock->shouldReceive('createOKResponse')
            ->with($this->dummyRequest, $this->dummyResource)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->put($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider baseExceptions
     */
    public function testPutShouldFailWithBaseException($exception)
    {
        $this->accessorMock->shouldReceive('put')->with(self::IRRELEVANT_KEY, array())->once()->andThrow($exception);
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with($exception->getCode(), $exception->getMessage())->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->put($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPutShouldFailWithAnyException()
    {
        $this->accessorMock->shouldReceive('put')->with(self::IRRELEVANT_KEY, array())->once()->andThrow('\Exception');
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with(500)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->put($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testDelete()
    {
        $this->accessorMock->shouldReceive('delete')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once();
        $this->messageProcessorMock->shouldReceive('createOKResponse')
            ->with($this->dummyRequest)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->delete($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider baseExceptions
     */
    public function testDeleteShouldFailWithBaseException($exception)
    {
        $this->accessorMock->shouldReceive('put')->with(self::IRRELEVANT_KEY, array())->once()->andThrow($exception);
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with($exception->getCode(), $exception->getMessage())->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->put($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testDeleteShouldFailWithAnyException()
    {
        $this->accessorMock->shouldReceive('put')->with(self::IRRELEVANT_KEY, array())->once()->andThrow('\Exception');
        $this->messageProcessorMock->shouldReceive('getRequestContentAsArray')->with($this->dummyRequest)->once()->andReturn(array());
        $this->messageProcessorMock->shouldReceive('createErrorResponse')
            ->with(500)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->put($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function baseExceptions()
    {
        return array(
            array(new Conflict()),
            array(new DataError()),
            array(new NoContent()),
            array(new NotFound())
        );
    }
}