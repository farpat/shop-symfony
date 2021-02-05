<?php

namespace App\Serializer\Normalizer;

use App\Entity\Category;
use App\Services\Shop\CategoryService;
use App\Services\Support\Arr;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategorySearchedNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private CategoryService $categoryService;
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Category $object
     * @param null $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $image = $object->getImage();
        return array_merge(
            Arr::get(['id', 'label'], $object),
            [
                'image' => $image ? $image->getUrlThumbnail() : null,
                'url'   => $this->urlGenerator->generate('app_front_category_show', [
                    'categoryId'   => $object->getId(),
                    'categorySlug' => $object->getSlug()
                ])
            ]
        );
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Category && $format === 'search';
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
