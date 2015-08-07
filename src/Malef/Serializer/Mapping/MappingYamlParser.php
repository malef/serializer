<?php

namespace Malef\Serializer\Mapping;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class MappingYamlParser
{
    /** @var OptionsResolver */
    protected $transformerMappingOptionsResolver;

    public function parseYamlModelMappings(Mapping $mapping, $mappingFilename)
    {
        $yamlParser = new Yaml();
        $rawModelMappings = $yamlParser->parse(file_get_contents($mappingFilename));

        foreach ($rawModelMappings as $rawModelMapping) {
            $modelClassName = $rawModelMapping['class_name'];
            if ($mapping->hasModelMapping($modelClassName)) {
                $modelMapping = $mapping->getModelMapping($modelClassName);
            } else {
                $modelMapping = new ModelMapping($modelClassName);
                $mapping->addModelMapping($modelMapping);
            }

            $rawFieldMappings = $rawModelMapping['fields'];
            $facets = [];
            foreach ($rawFieldMappings as $rawFieldMapping) {
                $facets = array_merge($facets, $rawFieldMapping['facets']);
            }
            foreach (array_unique($facets) as $facet) {
                $modelMapping->addFacetMapping($facet, new FacetMapping());
            }

            foreach ($rawFieldMappings as $rawFieldMapping) {
                $rawFieldMapping += [ // @todo Use OptionsResolver here.
                    'type' => 'mixed',
                    'embedded_facets' => [],
                    'strip_array_keys' => false,
                ];
                $transformerMappings = [];
                if (array_key_exists('transformers', $rawFieldMapping)) {
                    foreach ($rawFieldMapping['transformers'] as $rawTransformerMapping) {
                        $rawTransformerMapping = $this->getTransformerMappingOptionsResolver()->resolve($rawTransformerMapping);
                        $transformerMappings[] = new TransformerMapping($rawTransformerMapping['name'], $rawTransformerMapping['options']);
                    }
                }
                $fieldMapping = new FieldMapping(
                    $rawFieldMapping['getter'],
                    $rawFieldMapping['type'],
                    $rawFieldMapping['embedded_facets'],
                    $rawFieldMapping['strip_array_keys'],
                    $transformerMappings
                );
                foreach ($rawFieldMapping['facets'] as $facet) {
                    $modelMapping->getFacetMapping($facet)->addFieldMapping($rawFieldMapping['field'], $fieldMapping);
                }
            }
        }
    }

    public function getTransformerMappingOptionsResolver()
    {
        if (is_null($this->transformerMappingOptionsResolver)) {
            $transformerMappingOptionsResolver = new OptionsResolver();
            $transformerMappingOptionsResolver
                ->setRequired([
                    'name',
                ])
                ->setDefined([
                    'options',
                ])
                ->setDefaults([
                    'options' => [],
                ])
                ->setAllowedTypes([
                    'name' => 'string',
                    'options' => 'array',
                ]);

            $this->transformerMappingOptionsResolver = $transformerMappingOptionsResolver;
        }

        return $this->transformerMappingOptionsResolver;
    }
}
