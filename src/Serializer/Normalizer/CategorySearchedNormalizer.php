<?php

namespace App\Serializer\Normalizer;

use App\Entity\Category;
use App\Services\Shop\CategoryService;
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

    public function __construct (UrlGeneratorInterface $urlGenerator)
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
    public function normalize ($object, $format = null, array $context = []): array
    {
        $url = $this->urlGenerator->generate('app_category_show', [
            'categoryId'   => $object->getId(),
            'categorySlug' => $object->getSlug()
        ]);

        return [
            'id'    => $object->getId(),
            'label' => $object->getLabel(),
            'image' => $object->getImage() ? $object->getImage()->getUrlThumbnail() : null,
            'url'   => $url
        ];
    }

    public function supportsNormalization ($data, $format = null): bool
    {
        return $data instanceof Category && $format === 'search';
    }

    public function hasCacheableSupportsMethod (): bool
    {
        return true;
    }
}
