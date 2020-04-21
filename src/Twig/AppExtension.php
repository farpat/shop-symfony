<?php

namespace App\Twig;

use App\Services\NavigationService;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var NavigationService
     */
    private $navigationService;

    public function __construct (ContainerInterface $container, UrlGeneratorInterface $urlGenerator, NavigationService $navigationService)
    {
        $this->container = $container;
        $this->urlGenerator = $urlGenerator;
        $this->navigationService = $navigationService;
    }

    public function getFunctions (): array
    {
        return [
            new TwigFunction('get_asset', [$this, 'getAsset']),
            new TwigFunction('breadcrumb', [$this, 'getBreadcrumb'], ['is_safe' => ['html']]),
            new TwigFunction('active', [$this, 'isActive']),
        ];
    }

    public function isActive (string $route): string
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        return $request->getPathInfo() === $this->urlGenerator->generate($route) ? 'active' : '';
    }

    public function getAsset (string $asset, bool $isAbsolute = false): string
    {
        static $json;

        if (!$isAbsolute && $webpackDevServerPort = $this->container->getParameter('WEBPACK_DEV_SERVER_PORT')) {
            return substr($asset, -4) !== '.css' ? "http://localhost:$webpackDevServerPort/assets/$asset" : '';
        } else {
            $publicPath = $this->container->getParameter('kernel.project_dir') . '/public';
            $assetPath = $publicPath . '/assets';

            if (!$json) {
                $json = new Package(new JsonManifestVersionStrategy($assetPath . '/manifest.json'));
            }

            return $isAbsolute ? $publicPath . $json->getUrl($asset) : $json->getUrl($asset);
        }
    }

    public function getBreadcrumb (array $links): string
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
