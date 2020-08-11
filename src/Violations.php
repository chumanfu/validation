<?php declare(strict_types=1);

namespace Validation;

use Countable;

class Violations implements Countable
{
    protected $attributes = [];

    /**
     * Add a violation
     *
     * @param string $attribute
     * @param array<Constraint> $constraints
     */
    public function add(string $attribute, array $constraints): void
    {
        $this->attributes[$attribute] = $constraints;
    }

    /**
     * Format into more readable words
     *
     * @param string
     * @return string
     */
    protected function toWords(string $attribute): string
    {
        return str_replace(['-', '_', '.'], ' ', $attribute);
    }

    /**
     * Get array of messages for each attribute
     *
     * @return array
     */
    public function getMessages(): array
    {
        $messages = [];

        foreach ($this->attributes as $attribute => $constraints) {
            $messages[$attribute] = array_map(function ($constraint) use ($attribute) {
                return $constraint->getMessage($this->toWords($attribute));
            }, $constraints);
        }

        return $messages;
    }

    /**
     * Get all messages as a single line
     *
     * @return string
     */
    public function getMessagesLine(): string
    {
        return array_reduce($this->getMessages(), static function ($message, $messages) {
            return $message . implode(', ', $messages);
        }, '');
    }

    /**
     * Total number of violations
     *
     * @return int
     */
    public function count(): int
    {
        return array_reduce($this->attributes, static function ($carry, $constraints) {
            return $carry + count($constraints);
        }, 0);
    }
}
