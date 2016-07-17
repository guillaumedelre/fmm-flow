<?php

namespace Fmm\Flow\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FmmFlowBumpCommand extends AbstractFmmFlowCommand
{
    protected function configure()
    {
        $this
            ->setName('bump')
            ->setDescription('Bump a version tag')
            ->addArgument(
                'version',
                InputArgument::REQUIRED,
                'Version tag.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $version = $this->input->getArgument('version');

        $this->validateCurrentBranch();
        $this->validateTag($version);

        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_GH_PAGES, null));
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_MASTER, null));
        $this->git->git(sprintf(self::CMD_TAG_BRANCH, $version, null));

        $process = new Process('box.phar build');
        $process->start();
        while ($process->isRunning()) {
            $this->log('Build Phar processing...');
        }
        $this->log($process->getOutput(), self::LOG_LEVEL_COMMENT);

        $fs = new Filesystem();
        $fs->copy('build/fmm-flow.phar', "downloads/fmm-flow-$version.phar");

        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_GH_PAGES, null));

        $this->git->git("add downloads/fmm-flow-$version.phar");
        $sha1 = sha1_file("downloads/fmm-flow-$version.phar");
        $manifest = [
            'name' => "fmm-flow.phar",
            'sha1' => "$sha1",
            'url' => "http://guillaumedelre.github.io/fmm-flow/downloads/fmm-flow-$version.phar",
            'version' => "$version",
        ];
        $fs->dumpFile('manifest.json', json_encode($manifest));
        $this->git->git("add manifest.json");
        $this->git->git("commit -m 'Bump version $version'");

        $this->git->git(sprintf(self::CMD_PUSH_BRANCH, self::BRANCH_GH_PAGES, null));
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_MASTER, null));
        $this->git->git(self::CMD_PUSH_TAG);
    }

    private function validateCurrentBranch()
    {
        $currentBranch = $this->git->git('rev-parse --abbrev-ref HEAD');

        if (self::BRANCH_MASTER === $currentBranch) {
            $this->log(sprintf('You have to be on master branch currently on %s.', $currentBranch), self::LOG_LEVEL_ERROR);
            exit(1);
        }
    }

    private function validateTag($version)
    {
        $matches = [];
        if (preg_match('/^\d+\.\d+\.\d+(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?\$/',$version, $matches)) {
            $this->log(sprintf('Format of version tag is not invalid'), self::LOG_LEVEL_ERROR);
            exit(1);
        }
    }
}
