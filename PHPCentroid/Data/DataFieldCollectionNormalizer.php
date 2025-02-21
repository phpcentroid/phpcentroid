<?php

namespace PHPCentroid\Data;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * @method array getSupportedTypes(?string $format)
 */
class DataFieldCollectionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): array
    {
        if (!$object instanceof DataFieldCollection) {
            throw new UnexpectedValueException('The object must be an instance of DataFieldCollection.');
        }
        $data = [];
        $fieldNormalizer = new PropertyNormalizer();
        foreach ($object as $item) {
            $data[] = $fieldNormalizer->normalize($item, $format, $context);
        }
        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof DataFieldCollection;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): DataFieldCollection
    {
        if (!is_array($data)) {
            throw new UnexpectedValueException('Data expected to be an array.');
        }
        $collection = new DataFieldCollection();
        $fieldDenormalizer = new ObjectNormalizer();
        foreach ($data as $item) {
            $dataField = $fieldDenormalizer->denormalize($item, DataField::class, $format, $context);
            $collection->add(
                $dataField
            ); // You may want to denormalize each item here
        }
        return $collection;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === DataFieldCollection::class;
    }

}