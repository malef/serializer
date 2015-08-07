<?php

namespace Malef\Serializer\Transformer;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

class ExtractValueTransformer implements TransformerInterface
{
    const OPTION_KEY_GETTERS = 'getters';
    const OPTION_KEY_TRAVERSE = 'traverse';
    const OPTION_KEY_UNIQUE = 'unique';
    const OPTION_DEFAULT_VALUE_TRAVERSE = false;
    const OPTION_DEFAULT_VALUE_UNIQUE = false;

    /** @var OptionsResolver */
    protected $optionsResolver;

    /**
     * {@inheritdoc}
     */
    public function transform($value, array $options = [])
    {
        $options = $this->getOptionsResolver()->resolve($options);
        $getters = (array) $options[self::OPTION_KEY_GETTERS];

        if ($options[self::OPTION_KEY_TRAVERSE]) {
            foreach ($getters as $getter) {
                $value = $this->getExtractedTraversedValues($value, $getter);
            }
        } else {
            foreach ($getters as $getter) {
                $value = $this->getExtractedValue($value, $getter);
            }
        }

        if ($options[self::OPTION_KEY_UNIQUE] && (is_array($value) || $value instanceof Traversable)) {
            return array_values(array_unique($value));
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param string $getter
     * @throws TransformerException
     */
    protected function getExtractedValue($value, $getter)
    {
        if (is_array($value) || $value instanceof Traversable) {
            throw new TransformerException("Value should be array or instance of \\Traversable.");
        }

        return call_user_func([$value, $getter]);
    }

    /**
     * @param array|Traversable $values
     * @param string $getter
     * @return mixed[]
     */
    protected function getExtractedTraversedValues(array $values, $getter)
    {
        $allExtractedValues = [];
        foreach ($values as $value) {
            $extractedValues = call_user_func([$value, $getter]);
            if ($extractedValues instanceof Traversable) {
                $extractedValues = iterator_to_array($extractedValues, false);
            } elseif (!is_array($extractedValues)) {
                $extractedValues = [$extractedValues];
            }
            $allExtractedValues = array_values(array_merge(
                $allExtractedValues,
                $extractedValues
            ));
        }

        return $allExtractedValues;
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        if (is_null($this->optionsResolver)) {
            $optionsResolver = new OptionsResolver();
            $optionsResolver
                ->setRequired([
                    self::OPTION_KEY_GETTERS,
                ])
                ->setDefined([
                    self::OPTION_KEY_TRAVERSE,
                    self::OPTION_KEY_UNIQUE,
                ])
                ->setAllowedTypes([
                    self::OPTION_KEY_GETTERS => ['array', 'string'],
                    self::OPTION_KEY_TRAVERSE => 'bool',
                    self::OPTION_KEY_UNIQUE => 'bool',
                ])
                ->setDefaults([
                    self::OPTION_KEY_TRAVERSE => self::OPTION_DEFAULT_VALUE_TRAVERSE,
                    self::OPTION_KEY_UNIQUE => self::OPTION_DEFAULT_VALUE_UNIQUE,
                ]);

            $this->optionsResolver = $optionsResolver;
        }

        return $this->optionsResolver;
    }
}
