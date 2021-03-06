<?php

namespace App\Twig;

use App\Services\ModuleService;
use App\Services\NavigationService;
use App\Services\Support\Str;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    private UrlGeneratorInterface $urlGenerator;
    private NavigationService     $navigationService;
    private ParameterBagInterface $parameterBag;
    /**
     * @var ModuleService
     */
    private ModuleService $moduleService;

    public function __construct(
        ParameterBagInterface $parameterBag,
        UrlGeneratorInterface $urlGenerator,
        NavigationService $navigationService,
        ModuleService $moduleService
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->navigationService = $navigationService;
        $this->parameterBag = $parameterBag;
        $this->moduleService = $moduleService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_asset', [$this, 'getAsset']),
            new TwigFunction('breadcrumb', [$this, 'getBreadcrumb'], ['is_safe' => ['html']]),
            new TwigFunction('active', [$this, 'isActive']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'getPrice']),
        ];
    }

    public function getPrice(float $price): string
    {
        /** @var array{style: string, code: string, symbol:string} $currencyParameter */
        $currencyParameter = $this->moduleService->getParameter('billing', 'currency')->getValue();

        return Str::getFormattedPrice(
            $currencyParameter,
            $price
        );
    }

    public function isActive(Request $request, string $route): string
    {
        return $request->getPathInfo() === $this->urlGenerator->generate($route) ? ' active' : '';
    }

    public function getAsset(string $asset): string
    {
        static $json;

        $publicPath = $this->parameterBag->get('kernel.project_dir') . '/public';
        $assetPath = $publicPath . '/assets';
        if (is_file($assetPath . '/manifest.json')) {
            if (!$json) {
                $json = new Package(new JsonManifestVersionStrategy($assetPath . '/manifest.json'));
            }

            return $json->getUrl($asset);
        }

        $assetDevServerPort = $this->parameterBag->get('DOCKER_ASSET_DEV_SERVER_PORT');
        return substr($asset, -4) !== '.css' ? "http://localhost:$assetDevServerPort/assets/$asset" : '';
    }

    /**
     * @param array<int, array{label: string, 'url': ?string}> $links
     * @return string
     */
    public function getBreadcrumb(array $links): string
    {
        $linksCount = count($links);

        if ($linksCount === 0) {
            return '';
        }

        $liHtml = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        for ($i = 0; $i < $linksCount; $i++) {
            $label = ucfirst($links[$i]['label']);
            if ($i + 1 < $linksCount) {
                $liHtml .= "<li class='breadcrumb-item'><a href='{$links[$i]['url']}'>{$label}</a></li>";
            } else {
                $liHtml .= "<li class='breadcrumb-item' aria-current='page'>{$label}</li>";
            }
        }
        $liHtml .= '</nav></ol>';

        return $liHtml;
    }
}
