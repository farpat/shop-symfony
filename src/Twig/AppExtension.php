<?php

namespace App\Twig;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions (): array
    {
        return [
            new TwigFunction('get_asset', [$this, 'getAsset']),
        ];
    }

    public function getAsset (string $asset, bool $isAbsolute = false)
    {
        static $json;

        if ($webpackDevServerPort = $this->container->getParameter('WEBPACK_DEV_SERVER_PORT')) {
            return substr($asset, -4) !== '.css' ? "http://localhost:$webpackDevServerPort/assets/$asset" : '';
        } else {
            if (!$json) {
                $publicPath = $this->container->getParameter('kernel.project_dir') . '/public';
                $json = new Package(new JsonManifestVersionStrategy($publicPath . '/assets/manifest.json'));
            }

            return $json->getUrl("/$asset");
        }

    }
}
