<?php

namespace Malef\Serializer;

use Malef\Serializer\Mapping\Mapping;
use Malef\Serializer\Mapping\MappingYamlParser;

class SerializerYamlFactory
{
    /**
     * @param string[] $mappingFilenames
     * @return Serializer
     */
    public function create(array $mappingFilenames)
    {
        $mapping = new Mapping();
        $mappingParser = new MappingYamlParser();
        foreach ($mappingFilenames as $mappingFilename) {
            $mappingParser->parseYamlModelMappings($mapping, $mappingFilename);
        }

        return new Serializer($mapping);
    }
}
