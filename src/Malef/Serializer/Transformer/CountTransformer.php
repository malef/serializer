<?php

namespace Malef\Serializer\Transformer;

use Traversable;

class CountTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value, array $options = [])
    {
        if (is_array($value) || $value instanceof Traversable) {
            throw new TransformerException("Value should be array or instance of \\Traversable.");
        }

        if (is_array($value)) {
            return count($value);
        }

        return iterator_count($value);
    }
}
