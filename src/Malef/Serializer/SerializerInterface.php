<?php

namespace Malef\Serializer;

use Malef\Serializer\Transformer\TransformerInterface;

interface SerializerInterface
{
    /**
     * @param string $name
     * @param TransformerInterface $transformer
     * @return $this
     * @throws SerializerException
     */
    public function registerTransformer($name, TransformerInterface $transformer);

    /**
     * @param mixed $inputData
     * @param string|string[] $facetOrFacets
     * @return array
     * @throws SerializerException
     */
    public function serialize($inputData, $facetOrFacets);
}
