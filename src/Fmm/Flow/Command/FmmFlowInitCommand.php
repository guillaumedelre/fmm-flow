<?php

namespace Fmm\Flow\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FmmFlowInitCommand extends AbstractFmmFlowCommand
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize the working copy with fmm-flow')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->log('Initialize fmm-flow', self::LOG_LEVEL_COMMENT);
        $this->git->git('init');
        $this->git->git('commit --allow-empty -m "first commit"');
        $this->git->git(sprintf(self::CMD_CREATE_BRANCH, self::BRANCH_DEVELOP, self::BRANCH_MASTER));
    }

}
