<?php

namespace Malef\Serializer\Mapping;


class Mapping
{
    /**
     * @var ModelMapping[]
     */
    protected $modelMappings = [];

    /**
     * @param  ModelMapping $modelMapping
     * @return $this
     * @throws MappingException
     */
    public function addModelMapping(ModelMapping $modelMapping)
    {
        $modelClassName = $modelMapping->getClassName();
        if ($this->hasModelMapping($modelClassName)) {
            throw new MappingException("Mapping already exists for class '$modelClassName'.");
        }
        $this->modelMappings[$modelClassName] = $modelMapping;

        return $this;
    }

    /**
     * @param string $modelClassName
     * @return boolean
     */
    public function hasModelMapping($modelClassName)
    {
        return array_key_exists($modelClassName, $this->modelMappings);
    }

    /**
     * @param string $modelClassName
     * @return ModelMapping
     * @throws MappingException
     */
    public function getModelMapping($modelClassName)
    {
        if (!$this->hasModelMapping($modelClassName)) {
            throw new MappingException("Mapping missing for class '$modelClassName'.");
        }

        return $this->modelMappings[$modelClassName];
    }

    /**
     * @return ModelMapping[]
     */
    public function getModelMappings()
    {
        return $this->modelMappings;
    }

    /**
     * @param object $object
     * @return ModelMapping|null
     */
    public function getModelMappingByObject($object)
    {
        foreach ($this->modelMappings as $mappingClass => $mapping) {
            if ($object instanceof $mappingClass) {
                return $mapping;
            }
        }

        return null;
    }
}
