<?php

namespace Malef\Serializer\Mapping;

class FieldMapping
{
    /**
     * @var string
     */
    protected $getter;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $nullable;

    /**
     * @var string[]
     */
    protected $embeddedFacets;

    /**
     * @var boolean
     */
    protected $stripArrayKeys;

    /**
     * @var TransformerMapping[]
     */
    protected $transformerMappings = [];

    /**
     * @param string $getter
     * @param string $type
     * @param boolean $nullable
     * @param string[] $embeddedFacets
     * @param boolean $stripArrayKeys
     * @param TransformerMapping[] $transformerMappings
     */
    public function __construct($getter, $type, $nullable, $embeddedFacets, $stripArrayKeys, array $transformerMappings)
    {
        $this->getter = $getter;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->embeddedFacets = $embeddedFacets;
        $this->stripArrayKeys = $stripArrayKeys;
        foreach ($transformerMappings as $transformerMapping) {
            $this->addTransformerMapping($transformerMapping);
        }
    }

    /**
     * @return string
     */
    public function getGetter()
    {
        return $this->getter;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @return string
     */
    public function getEmbeddedFacets()
    {
        return $this->embeddedFacets;
    }

    /**
     * @return boolean
     */
    public function getStripArrayKeys()
    {
        return $this->stripArrayKeys;
    }

    /**
     * @return TransformerMapping[]
     */
    public function getTransformerMappings()
    {
        return $this->transformerMappings;
    }

    /**
     * @param TransformerMapping $transformerMapping
     * @return $this
     */
    protected function addTransformerMapping(TransformerMapping $transformerMapping)
    {
        $this->transformerMappings[] = $transformerMapping;

        return $this;
    }
}
