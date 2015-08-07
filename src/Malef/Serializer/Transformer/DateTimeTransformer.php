<?php

namespace Malef\Serializer\Transformer;

use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeTransformer implements TransformerInterface
{
    const OPTION_KEY_FORMAT = 'format';
    const OPTION_KEY_ALLOW_NULL = 'allow_null';
    const OPTION_DEFAULT_VALUE_FORMAT = 'Y-m-d H:i:s';
    const OPTION_DEFAULT_VALUE_ALLOW_NULL = false;

    /** @var OptionsResolver */
    protected $optionsResolver;

    /**
     * {@inheritdoc}
     */
    public function transform($value, array $options = [])
    {
        $options = $this->getOptionsResolver()->resolve($options);

        if (!$value instanceof DateTime) {
            if (!$options[self::OPTION_KEY_ALLOW_NULL]) {
                throw new TransformerException("Null value not allowed.");
            }

            return null;
        }

        return $value->format($options[self::OPTION_KEY_FORMAT]);
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        if (is_null($this->optionsResolver)) {
            $optionsResolver = new OptionsResolver();
            $optionsResolver
                ->setDefined([
                    self::OPTION_KEY_FORMAT,
                    self::OPTION_KEY_ALLOW_NULL,
                ])
                ->setAllowedTypes([
                    self::OPTION_KEY_FORMAT => 'string',
                    self::OPTION_KEY_ALLOW_NULL => 'bool',
                ])
                ->setDefaults([
                    self::OPTION_KEY_FORMAT => self::OPTION_DEFAULT_VALUE_FORMAT,
                    self::OPTION_KEY_ALLOW_NULL => self::OPTION_DEFAULT_VALUE_ALLOW_NULL,
                ]);

            $this->optionsResolver = $optionsResolver;
        }

        return $this->optionsResolver;
    }
}
