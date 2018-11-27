<?php

namespace App\Model;

final class Colour implements \Serializable, \JsonSerializable
{
    const RED = 'red';
    const GREEN = 'green';
    const BLUE = 'blue';

    const COLOURS = [self::RED, self::GREEN, self::BLUE];

    private static $instances = [];

    /** @var string */
    private $name;

    private function __construct(string $name)
    {
        // Note: In future this could be more useful in its own function should validation may be desired without instantiation.
        if (!in_array($name, self::COLOURS, true)) {
            $colours = implode(', ', self::COLOURS);
            throw new \UnexpectedValueException("{$name} is not a valid colour ({$colours}).");
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function getByName(string $name): self
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        return self::$instances[$name] = new self($name);
    }

    public function serialize()
    {
        return serialize($this->name);
    }

    public function unserialize($data)
    {
        $this->__construct(unserialize($data));
    }

    public function jsonSerialize()
    {
        return $this->name;
    }
}
