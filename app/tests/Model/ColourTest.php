<?php

namespace App\Tests\Model\Colour;

use App\Model\Colour;
use PHPUnit\Framework\TestCase;

class ColourTest extends TestCase
{
    public function testGetByName(): void
    {
        $colour1 = Colour::getByName(Colour::RED);
        $colour2 = Colour::getByName(Colour::RED);
        $this->assertEquals($colour1, $colour2);
    }

    public function testGetName(): void
    {
        $colour = Colour::getByName(Colour::BLUE);
        $this->assertEquals(Colour::BLUE, $colour->getName());
    }

    public function testInvalidName(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        Colour::getByName('invalid');
    }
}
