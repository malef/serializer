<?php

namespace Malef\Serializer\Mapping;


class ModelMapping
{
    /** @var string */
    protected $className;

    /** @var FacetMapping[] */
    protected $facetMappings = [];

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $facet
     * @param FacetMapping $facetMapping
     * @return $this
     */
    public function addFacetMapping($facet, FacetMapping $facetMapping)
    {
        $this->facetMappings[$facet] = $facetMapping;

        return $this;
    }

    /**
     * @param string $facet
     * @return FacetMapping
     * @throws MappingException
     */
    public function getFacetMapping($facet)
    {
        if (!array_key_exists($facet, $this->facetMappings)) {
            throw new MappingException("Facet mapping not found for facet '$facet'.");
        }

        return $this->facetMappings[$facet];
    }

    /**
     * @param string[] $facets
     * @return MultiFacetMapping
     */
    public function getMultiFacetMapping(array $facets)
    {
        $multiFacetMapping = new MultiFacetMapping();
        foreach ($facets as $facet) {
            $multiFacetMapping->addFacetMapping($this->getFacetMapping($facet));
        }

        return $multiFacetMapping;
    }
}
