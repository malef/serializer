<?php

namespace Malef\Serializer;

use Malef\Serializer\Mapping\FieldMapping;
use Malef\Serializer\Mapping\Mapping;
use Malef\Serializer\Mapping\MappingException;
use Malef\Serializer\Transformer\TransformerException;
use Malef\Serializer\Transformer\TransformerInterface;
use Traversable;

class Serializer
{
    /** @var Mapping */
    protected $mapping;

    /** @var TransformerInterface[] */
    protected $transformers = [];

    /**
     * @param Mapping $mapping
     */
    public function __construct(Mapping $mapping)
    {
        $this->serializeping = $mapping;
    }

    /**
     * {@inheritdoc}
     */
    public function registerTransformer($name, TransformerInterface $transformer)
    {
        if (array_key_exists($name, $this->transformers)) {
            throw new SerializerException("Transformer with name '$name' already registered.");
        }
        $this->transformers[$name] = $transformer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($inputData, $facetOrFacets)
    {
        $facets = (array) $facetOrFacets;

        if (is_array($inputData)) {
            $outputData = [];
            foreach ($inputData as $inputDataKey => $inputDataItem) {
                try {
                    $outputData[$inputDataKey] = $this->serialize($inputDataItem, $facets);
                } catch (MappingException $e) {
                    throw new SerializerException("Failed to map array on item with key '$inputDataKey'.", 0, $e);
                }
            }

            return $outputData;
        }

        if (is_object($inputData)) {
            $outputData = [];
            foreach ($this->getFieldMappingByObjectAndFacets($inputData, $facets) as $field => $fieldMapping) {
                try {
                    $outputData[$field] = $this->serializeObjectField($inputData, $fieldMapping, $facets);
                } catch (MappingException $e) {
                    $className = get_class($inputData);

                    throw new SerializerException("Failed to map object of class '$className' on field '$field'.", 0, $e);
                }
            }

            return $outputData;
        }

        return $inputData;
    }

    /**
     * @param mixed $object
     * @param FieldMapping $fieldMapping
     * @param string []$facets
     * @return mixed
     * @throws SerializerException
     */
    protected function serializeObjectField($object, FieldMapping $fieldMapping, array $facets)
    {
        $value = call_user_func([$object, $fieldMapping->getGetter()]);

        if (is_null($value) && $fieldMapping->isNullable()) {
            return null;
        }

        if (
            ('mixed' === $fieldMapping->getType())
            || ('bool' === $fieldMapping->getType() && is_bool($value))
            || ('string' === $fieldMapping->getType() && is_string($value))
            || ('integer' === $fieldMapping->getType() && is_integer($value))
            || ('float' === $fieldMapping->getType() && is_float($value))
        ) {
            return $this->transform($value, $fieldMapping);
        }

        if (is_array($value) && $this->isArrayMatchingFieldMappingType($value, $fieldMapping)) {
            return $this->serialize(
                $this->transform(
                    $fieldMapping->getStripArrayKeys() ? array_values($value) : $value,
                    $fieldMapping
                ),
                $fieldMapping->getEmbeddedFacets() ?: $facets
            );
        }

        if (is_object($value) && $this->isObjectMatchingFieldMappingType($value, $fieldMapping)) {
            return $this->serialize(
                $this->transform($value, $fieldMapping),
                $fieldMapping->getEmbeddedFacets() ?: $facets
            );
        }

        if (is_object($value) && $value instanceof Traversable) {
            $valueToArray = iterator_to_array($value);
            if ($this->isArrayMatchingFieldMappingType($valueToArray, $fieldMapping)) {
                return $this->serialize(
                    $this->transform(
                        $fieldMapping->getStripArrayKeys() ? array_values($valueToArray) : $valueToArray,
                        $fieldMapping
                    ),
                    $fieldMapping->getEmbeddedFacets() ?: $facets
                );
            }
        }

        throw new SerializerException("Object field not mapped.");
    }

    /**
     * @param mixed $value
     * @param FieldMapping $fieldMapping
     * @return mixed
     */
    protected function transform($value, FieldMapping $fieldMapping)
    {
        foreach ($fieldMapping->getTransformerMappings() as $transformerMapping) {
            $transformerName = $transformerMapping->getName();
            $transformer = $this->getTransformer($transformerName);
            try {
                $value = $transformer->transform($value, $transformerMapping->getOptions());
            } catch (TransformerException $e) {
                throw new SerializerException("Failed to transform with transformer '$transformerName'.", 0, $e);
            }
        }

        return $value;
    }

    /**
     * @param array $array
     * @param FieldMapping $fieldMapping
     * @return boolean
     */
    protected function isArrayMatchingFieldMappingType(array $array, FieldMapping $fieldMapping)
    {
        $fieldMappingType = $fieldMapping->getType();

        if ('array' === $fieldMappingType) {
            return true;
        }

        if (1 != preg_match('/^array\<(?P<className>[^\>]+)\>/', $fieldMappingType, $fieldMappingTypeMatches)) {
            return false;
        }

        foreach ($array as $arrayItem) {
            if (get_class($arrayItem) != $fieldMappingTypeMatches['className']) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param mixed $object
     * @param FieldMapping $fieldMapping
     * @return boolean
     */
    protected function isObjectMatchingFieldMappingType($object, FieldMapping $fieldMapping)
    {
        $type = $fieldMapping->getType();

        return ($object instanceof $type);
    }

    /**
     * @param mixed $object
     * @param string[] $facets
     * @return FieldMapping[]
     * @throws SerializerException
     */
    protected function getFieldMappingByObjectAndFacets($object, array $facets)
    {
        $className = get_class($object);
        $modelMapping = $this->serializeping->getModelMappingByObject($object);
        if (!$modelMapping) {
            throw new SerializerException("Missing model mapping for class name '$className'.");
        }

        return $modelMapping->getMultiFacetMapping($facets)->getFieldMappings();
    }

    /**
     * @param string $name
     * @return TransformerInterface
     * @throws SerializerException
     */
    protected function getTransformer($name)
    {
        if (!array_key_exists($name, $this->transformers)) {
            throw new SerializerException("No transformer with name '$name'.");
        }

        return $this->transformers[$name];
    }
}
