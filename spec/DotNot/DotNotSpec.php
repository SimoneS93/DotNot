<?php

namespace spec\DotNot;

use DotNot\Exceptions\DotNotException;
use Mockery as m;
use PhpSpec\ObjectBehavior;


class DotNotSpec extends ObjectBehavior
{
    
    function it_gets_first_level_array_value()
    {
        $this->beConstructedWith(['name' => 'Simone Salerno']);
        
        $this->get('name')->shouldBe('Simone Salerno');
    }
    
    
    function it_gets_first_level_stdclass_property()
    {
        $this->beConstructedWith((object) ['name' => 'Simone Salerno']);
        
        $this->get('name')->shouldBe('Simone Salerno');
    }
    
    
    function it_gets_first_level_custom_class_property()
    {
        $mock = m::mock('object');
        $mock->name = 'Simone Salerno';
        
        $this->beConstructedWith($mock);
        
        $this->get('name')->shouldBe('Simone Salerno');
    }
    
    
    function it_gets_first_level_custom_class_property_via_getter()
    {
        $mock = m::mock('object');
        $mock->shouldReceive('getName')->andReturn('Simone Salerno');
        
        $this->beConstructedWith($mock);
        
        $this->get('name')->shouldBe('Simone Salerno');
    }
    
    
    function it_gets_second_level_array_value()
    {
        $this->beConstructedWith(['author' => ['name' => 'Simone Salerno']]);
        
        $this->get('author.name')->shouldBe('Simone Salerno');
    }
    
    
    function it_gets_second_level_stdclass_property()
    {
        $this->beConstructedWith((object) ['author' => (object) ['name' => 'Simone Salerno']]);
        
        $this->get('author.name')->shouldBe('Simone Salerno');
    }
    
    
    function it_gets_arbitrary_chained_value()
    {
        $mock = m::mock('object');
        $mock->name = 'Simone Salerno';
        
        $this->beConstructedWith(['people' => [
            0 => (object) [
                'author' => $mock
            ]
        ]]);
        
        $this->get('people.0.author.name')->shouldBe('Simone Salerno');
    }
    
    function it_tests_chain_existence()
    {
        $mock = m::mock('object');
        $mock->name = 'Simone Salerno';
        
        $this->beConstructedWith(['people' => [
            0 => (object) [
                'author' => $mock
            ]
        ]]);
        
        $this->has('people.0.author.name')->shouldBe(true);
    }
    
    function it_tests_first_level_non_existence()
    {
        $this->beConstructedWith(['name' => 'Simone Salerno']);
        
        $this->has('foo')->shouldBe(false);
    }
    
    function it_allows_array_access()
    {
        $this->beConstructedWith(['author' => ['name' => 'Simone Salerno']]);
        
        $this['author.name']->shouldBe('Simone Salerno');
    }
    
    function it_tests_array_access_existence()
    {
        $this->beConstructedWith(['author' => ['name' => 'Simone Salerno']]);
        
        $this->shouldHaveKey('author.name');
    }
    
    function it_throws_exception_on_not_found()
    {
        $this->beConstructedWith(['author' => ['name' => 'Simone Salerno']]);
        
        $this->shouldThrow(DotNotException::class)->duringGet('foo');
    }
    
    function it_throws_exception_on_array_set()
    {
        $this->beConstructedWith([]);
        
        try {
            $this['name'] = 'Simone Salerno';
            throw new \Exception;
        }
        catch (DotNotException $ex) { }
        
    }
}
