<?php

namespace App\Serializer\Denormalizer;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Serializer\Exception\NotFoundEntityException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizer for convert Category.id to the object
 */
class CompanyDenormalizer implements DenormalizerInterface
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    /**
     * @throws NotFoundEntityException
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Category
    {
        $result = $this->categoryRepository->find($data);
        if (!$result) {
            throw new NotFoundEntityException('Not found category entity for id "'.$data.'"');
        }

        return $result;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return $type === Category::class;
    }
}
