<?php

namespace Malef\Serializer\Mapping;


class MultiFacetMapping implements GenericFacetMappingInterface
{
    /** @var FieldMapping[] */
    protected $fieldMappings = [];

    /**
     * @param FacetMapping $facetMapping
     * @return $this
     */
    public function addFacetMapping(FacetMapping $facetMapping)
    {
        foreach ($facetMapping->getFieldMappings() as $field => $fieldMapping) {
            if (
                    array_key_exists($field, $this->fieldMappings)
                    && $fieldMapping !== $this->fieldMappings[$field] // TODO Improve, check actual identicality.
            ) {
                throw new MappingException("Field mapping not found for field '$field'.");
            }
            $this->fieldMappings[$field] = $fieldMapping;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldMappings()
    {
        return $this->fieldMappings;
    }
}
