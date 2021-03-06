<?php

declare(strict_types=1);

namespace Antidot\SymfonyConfigTranslator;

use function str_replace;
use function strpos;

class ArgumentTranslator
{
    public function process(array $config, array $service): array
    {
        $arguments = [];
        /**
         * @var string $argument
         */
        foreach ($service['arguments'] as $argument => $value) {
            $arguments[str_replace('$', '', $argument)] = $this->getArguments($config, $value);
        }

        return $arguments;
    }

    /**
     * @param array<mixed> $config
     * @param mixed $value
     * @return array<mixed>|mixed|string|string[]
     */
    private function getArguments(array $config, $value)
    {
        if (is_array($value)) {
            return $value;
        }

        $isService = 0 === strpos($value, '@');
        if ($isService) {
            return str_replace('@', '', $value);
        }
        /** @var int|string $index */
        $index = str_replace(['%config%', '%config.', '%'], '', $value);
        if (array_key_exists($index, $config)) {
            return $config[$index];
        }

        return $value;
    }
}
