<?php

namespace spec\DotNot;

use PhpSpec\ObjectBehavior;
use Mockery as m;


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
}
