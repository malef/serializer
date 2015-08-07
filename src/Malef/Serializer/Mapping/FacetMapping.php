<?php

namespace Malef\Serializer\Mapping;

class FacetMapping implements GenericFacetMappingInterface
{
    /**
     * @var FieldMappings[]
     */
    protected $fieldMappings = [];

    /**
     * @param  string       $field
     * @param  FieldMapping $fieldMapping
     * @return $this
     */
    public function addFieldMapping($field, FieldMapping $fieldMapping)
    {
        $this->fieldMappings[$field] = $fieldMapping;

        return $this;
    }

    /**
     * @return FieldMapping[]
     */
    public function getFieldMappings()
    {
        return $this->fieldMappings;
    }
}
