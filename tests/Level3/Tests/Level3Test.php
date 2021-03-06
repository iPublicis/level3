<?php

namespace Level3\Tests;

use Level3\Level3;
use Mockery as m;

class Level3Test extends TestCase
{
    public function setUp()
    {
        $this->mapperMock = $this->createMapperMock();
        $this->hubMock = $this->createHubMock();
        $this->hubMock->shouldReceive('setLevel3')->once()->andReturn(null);

        $this->processorMock = $this->createProcessorMock();
        $this->processorMock->shouldReceive('setLevel3')->once()->andReturn(null);

        $this->level3 = new Level3($this->mapperMock, $this->hubMock, $this->processorMock);
    }

    protected function createWrapperMock()
    {
        $wrapper = parent::createWrapperMock();
        $wrapper->shouldReceive('setLevel3')->with($this->level3)->once()->andReturn(null);

        return $wrapper;
    }

    public function testGetDebug()
    {
        $this->level3->setDebug(true);
        $this->assertTrue($this->level3->getDebug());
    }

    public function testGetHub()
    {
        $this->assertSame($this->hubMock, $this->level3->getHub());
    }

    public function testGetMapper()
    {
        $this->assertSame($this->mapperMock, $this->level3->getMapper());
    }

    public function testGetProcessor()
    {
        $this->assertSame($this->processorMock, $this->level3->getProcessor());
    }

    public function testGetRepository()
    {
        $repository = $this->createRepositoryMock();
        $this->hubMock->shouldReceive('get')->once()->with('foo')->andReturn($repository);

        $this->assertSame($repository, $this->level3->getRepository('foo'));
    }

    public function testGetURI()
    {
        $attributes = $this->createParameterBagMock();
        $this->mapperMock->shouldReceive('getURI')->once()->with('foo', 'bar', $attributes)->andReturn('foo');

        $this->assertSame('foo', $this->level3->getURI('foo', 'bar', $attributes));
    }

    public function testAddProcessorWrapperBoth()
    {
        $wrapperA = $this->createWrapperMock();
        $wrapperB = $this->createWrapperMock();

        $this->level3->addProcessorWrapper($wrapperA, Level3::PRIORITY_LOW);
        $this->level3->addProcessorWrapper($wrapperB, Level3::PRIORITY_HIGH);

        $result = $this->level3->getProcessorWrappers();
        $this->assertSame($wrapperA, $result[0]);
        $this->assertSame($wrapperB, $result[1]);
        $this->assertCount(2, $result);
    }

    public function testAddProcessorWrapperDefault()
    {
        $wrapperA = $this->createWrapperMock();
        $wrapperB = $this->createWrapperMock();

        $this->level3->addProcessorWrapper($wrapperA);
        $this->level3->addProcessorWrapper($wrapperB);

        $result = $this->level3->getProcessorWrappers();
        $this->assertSame($wrapperA, $result[0]);
        $this->assertSame($wrapperB, $result[1]);
        $this->assertCount(2, $result);
    }

    public function testAddProcessorWrapperOne()
    {
        $wrapperA = $this->createWrapperMock();
        $wrapperB = $this->createWrapperMock();

        $this->level3->addProcessorWrapper($wrapperA);
        $this->level3->addProcessorWrapper($wrapperB, Level3::PRIORITY_HIGH);

        $result = $this->level3->getProcessorWrappers();
        $this->assertSame($wrapperA, $result[0]);
        $this->assertSame($wrapperB, $result[1]);
        $this->assertCount(2, $result);
    }

    public function testClearProcessorWrapper()
    {
        $wrapperA = $this->createWrapperMock();
        $wrapperB = $this->createWrapperMock();

        $this->level3->addProcessorWrapper($wrapperA);
        $this->level3->addProcessorWrapper($wrapperB);

        $result = $this->level3->getProcessorWrappers();
        $this->assertCount(2, $result);

        $this->level3->clearProcessWrappers();
        $result = $this->level3->getProcessorWrappers();
        $this->assertCount(0, $result);
    }

    public function testGetProcessorWrappersByClass()
    {
        $wrapperA = $this->createWrapperMock();
        $this->assertNull($this->level3->getProcessorWrappersByClass(get_class($wrapperA)));

        $this->level3->addProcessorWrapper($wrapperA);

        $this->assertSame(
            $wrapperA,
            $this->level3->getProcessorWrappersByClass(get_class($wrapperA))
        );
    }

    public function testGetFormatter()
    {
        $formatter = $this->createFormatWriterMock();
        $this->assertSame([], $this->level3->getFormatWriter());

        $this->level3->addFormatWriter($formatter);
        $this->assertSame(['foo/bar' => $formatter], $this->level3->getFormatWriter());
    }

    public function testGetFormatterByContentTypeDefault()
    {
        $formatterA = m::mock('Level3\Resource\Format\Writer');
        $formatterA->shouldReceive('getContentType')->andReturn('bar/bar');
        $this->level3->addFormatWriter($formatterA);

        $formatterB = m::mock('Level3\Resource\Format\Writer');
        $formatterB->shouldReceive('getContentType')->andReturn('foo/foo');
        $this->level3->addFormatWriter($formatterB);

        $this->assertSame($formatterA, $this->level3->getFormatWriterByContentType('*/*'));
    }

    public function testGetFormatterByContentTypeSetDefault()
    {
        $formatterA = m::mock('Level3\Resource\Format\Writer');
        $formatterA->shouldReceive('getContentType')->andReturn('bar/bar');
        $this->level3->addFormatWriter($formatterA);

        $formatterB = m::mock('Level3\Resource\Format\Writer');
        $formatterB->shouldReceive('getContentType')->andReturn('foo/foo');
        $this->level3->addFormatWriter($formatterB);

        $this->level3->setDefaultFormatWriter('foo/foo');

        $this->assertSame($formatterB, $this->level3->getFormatWriterByContentType('*/*'));
    }

    public function testGetFormatterByContentType()
    {
        $formatter = $this->createFormatWriterMock();
        $this->assertNull($this->level3->getFormatWriterByContentType('foo/bar'));

        $this->level3->addFormatWriter($formatter);
        $this->assertSame($formatter, $this->level3->getFormatWriterByContentType('foo/bar'));
    }

    public function testBoot()
    {
        $this->mapperMock->shouldReceive('boot')->with($this->hubMock)->once()->andReturn(null);

        $this->level3->boot();
    }
}
