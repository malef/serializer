<?php

namespace Malef\Serializer\Transformer;

interface TransformerInterface
{
    /**
     * @param mixed $value
     * @param array $options
     */
    public function transform($value, array $options = []);
}
