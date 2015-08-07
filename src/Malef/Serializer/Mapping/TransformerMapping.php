<?php

namespace Malef\Serializer\Mapping;

class TransformerMapping
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $type
     * @param array $options
     */
    public function __construct($type, array $options)
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
