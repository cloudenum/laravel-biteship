<?php

namespace Cloudenum\Biteship\Tests;

use Cloudenum\Biteship\BiteshipObject;

class BiteshipObjectTest extends TestCase
{
    public function testGetAttribute()
    {
        $object = new class extends BiteshipObject
        {
            protected array $attributes = [];
        };

        $object->setAttribute('name', 'John');
        $object->setAttribute('age', 30);

        $this->assertEquals('John', $object->getAttribute('name'));
        $this->assertEquals(30, $object->getAttribute('age'));
        $this->assertNull($object->getAttribute('address'));
        $this->assertEquals('Default', $object->getAttribute('address', 'Default'));
    }

    public function testSetAttribute()
    {
        $object = new class extends BiteshipObject
        {
            protected array $attributes = [];
        };

        $object->setAttribute('name', 'John');
        $object->setAttribute('age', 30);

        $this->assertEquals('John', $object->getAttribute('name'));
        $this->assertEquals(30, $object->getAttribute('age'));
    }

    public function testFillDynamicProperties()
    {
        $object = new class extends BiteshipObject
        {
            protected array $attributes = [];

            protected array $dynamicProperties = ['name', 'age'];
        };

        $object->fillDynamicProperties([
            'name' => 'John',
            'age' => 30,
            'address' => '123 Main St',
        ]);

        $this->assertEquals('John', $object->getAttribute('name'));
        $this->assertEquals(30, $object->getAttribute('age'));
        $this->assertNull($object->getAttribute('address'));
    }

    public function testIsDynamicProperty()
    {
        $object = new class extends BiteshipObject
        {
            protected array $attributes = [];

            protected array $dynamicProperties = ['name', 'age'];
        };

        $this->assertTrue($object->isDynamicProperty('name'));
        $this->assertTrue($object->isDynamicProperty('age'));
        $this->assertFalse($object->isDynamicProperty('address'));
    }

    public function testAccessDynamicProperties()
    {
        $object = new class extends BiteshipObject
        {
            protected array $attributes = [];

            protected array $dynamicProperties = ['name', 'age'];
        };

        $object->name = 'John';
        $object->age = 30;

        $this->assertEquals('John', $object->name);
        $this->assertEquals(30, $object->age);

        $this->expectException(\InvalidArgumentException::class);
        $object->address;
    }

    public function testObjectSerializeAndUnserialize()
    {
        // use any random class that extend the BiteshipObject class
        $object = new \Cloudenum\Biteship\Courier();

        $object->courier_name = 'Grab';
        $object->available_for_proof_of_delivery = true;

        $serialized = serialize($object);
        $unserialized = unserialize($serialized);

        $this->assertEquals($object->courier_name, $unserialized->courier_name);
        $this->assertEquals($object->available_for_proof_of_delivery, $unserialized->available_for_proof_of_delivery);
    }

    public function testJsonSerialize()
    {
        $object = new class extends BiteshipObject
        {
            protected array $attributes = [];

            protected array $dynamicProperties = ['name', 'age'];
        };

        $object->name = 'John';
        $object->age = 30;

        $this->assertEquals('{"name":"John","age":30}', json_encode($object));
        $this->assertJson(json_encode($object));
    }
}
