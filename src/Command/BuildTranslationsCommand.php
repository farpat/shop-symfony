<?php

namespace App\Command;

use App\Services\Support\Str;
use Symfony\Bundle\FrameworkBundle\Command\TranslationUpdateCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class BuildTranslationsCommand extends Command
{
    private const JS_LANG = 'js-lang';
    private const LOCALES = ['fr'];

    protected static $defaultName = 'app:build-translations';
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct (string $name = null, ParameterBagInterface $parameterBag, Filesystem $filesystem)
    {
        $this->parameterBag = $parameterBag;
        $this->filesystem = $filesystem;

        parent::__construct($name);
    }

    protected function configure ()
    {
        $this
            ->setDescription('Build translations in assets/' . self::JS_LANG);
    }

    protected function execute (InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $updateTranslationCommand = $this->getApplication()->find('translation:update');

        foreach (self::LOCALES as $locale) {
            $this->generateJson($updateTranslationCommand, $output, $locale);
        }

        $jsLangPath = $this->parameterBag->get('kernel.project_dir') . '/assets/' . self::JS_LANG;

        if ($this->filesystem->exists($jsLangPath)) {
            $this->filesystem->remove($jsLangPath);
        }

        $files = $this->getFiles();
        $this->filesystem->mkdir($jsLangPath);
        foreach ($files as $file) {
            $io->comment("Handling $file");
            $this->compileTranslation($file);
        }

        $io->success('The translations build were successfully done.');

        return 0;
    }

    /**
     * @param string $locale
     *
     * @return int Command exit code
     */
    private function generateJson (TranslationUpdateCommand $command, OutputInterface $output, string $locale): int
    {
        $arguments = [
            'command'         => 'translation:update',
            '--force'         => true,
            '--output-format' => 'json',
            'locale'          => $locale
        ];

        $updateTranslationInput = new ArrayInput($arguments);
        return $command->run($updateTranslationInput, $output);
    }

    private function getFiles (): array
    {
        $files = [];
        $scandirFiles = array_slice(scandir($this->parameterBag->get('kernel.project_dir') . '/translations'), 2);

        foreach ($scandirFiles as $file) {
            if (Str::endsWith($file, '.json')) {
                $files[] = $this->parameterBag->get('kernel.project_dir') . '/translations/' . $file;
            }
        }

        return $files;
    }

    private function compileTranslation (string $file)
    {
        preg_match_all('/([a-z\+\-]+)\.([a-z]{1,3})\.json$/', $file, $matches);
        $lang = $matches[2][0];
        $filename = str_replace('+intl-icu', '', $matches[1][0]) . '.json';
        $finalLangDirectory = $this->parameterBag->get('kernel.project_dir') . '/assets/' . self::JS_LANG . '/' . $lang;

        if (!$this->filesystem->exists($finalLangDirectory)) {
            $this->filesystem->mkdir($finalLangDirectory);
        }

        ob_start();
        require($file);
        $ob = ob_get_clean();
        $data = json_encode(json_decode($ob)); //to minify the file

        file_put_contents($finalLangDirectory . '/' . $filename, $data);
    }
}
