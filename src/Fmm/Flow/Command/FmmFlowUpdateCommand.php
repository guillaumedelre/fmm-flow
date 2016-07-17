<?php

namespace Fmm\Flow\Command;

use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FmmFlowUpdateCommand extends AbstractFmmFlowCommand
{
    const MANIFEST_FILE = 'https://guillaumedelre.github.io/fmm-flow/manifest.json';

    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Updates fmm-flow.phar to the latest version')
            ->addOption('major', null, InputOption::VALUE_NONE, 'Allow major version update')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->log('Looking for updates...');

        try {
            $manifestFile = Manifest::loadFile(self::MANIFEST_FILE);
        } catch (\Exception $e) {
            $this->log($e->getMessage(), self::LOG_LEVEL_ERROR);
            exit(1);
        }

        try {
            $manager = new Manager($manifestFile);
        } catch (FileException $e) {
            $this->log('Unable to search for updates', self::LOG_LEVEL_ERROR);
            exit(1);
        }

        $currentVersion = $this->getApplication()->getVersion();
        $allowMajor = $input->getOption('major');

        if ($manager->update($currentVersion, $allowMajor)) {
            $this->log('Updated to latest version', self::LOG_LEVEL_INFO);
        } else {
            $this->log('Already up-to-date', self::LOG_LEVEL_COMMENT);
        }
    }
}
