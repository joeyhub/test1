<?php

namespace App\Model;

// Note: This would work better if it made use of a proper set (data structure) class.
final class Strip implements \Serializable, \JsonSerializable
{
    private $colours = [];

    public function addColour(Colour $colour): self
    {
        $name = $colour->getName();

        if (isset($colours[$name])) {
            // Note: This would be better off with a custom exception class expressing that the message can be shown to the user and possibly allowing the determination of an error code.
            throw new \DomainException("Colour {$name} already exists.");
        }

        $this->colours[$name] = $colour;

        return $this;
    }

    public function serialize()
    {
        return serialize($this->colours);
    }

    public function unserialize($data)
    {
        foreach (unserialize($data) as $colour) {
            $this->addColour($colour);
        }
    }

    public function jsonSerialize()
    {
        return array_values($this->colours);
    }

    public static function jsonUnserialize(string ...$data): self
    {
        if (0 === count($data)) {
            throw new \DomainException('There must be at least one colour set.');
        }

        $colourSet = new self();

        foreach ($data as $colour) {
            $colourSet->addColour(Colour::getByName($colour));
        }

        return $colourSet;
    }
}
