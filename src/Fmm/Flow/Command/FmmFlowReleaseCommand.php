<?php

namespace Fmm\Flow\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FmmFlowReleaseCommand extends AbstractFmmFlowCommand
{
    const ARG_START = 'start';
    const ARG_FINISH = 'finish';
    const ARG_PUBLISH = 'publish';
    const ARG_RETRIEVE = 'retrieve';

    protected function configure()
    {
        $this
            ->setName('release')
            ->setDescription('Perform an action (start|publish|retrieve|finish) with a release branch')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'Action to perform (start|publish|retrieve|finish).'
            )
            ->addArgument(
                'releaseName',
                InputArgument::REQUIRED,
                'Name of the release branch.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        switch ($this->input->getArgument('action')) {
            case self::ARG_START:
                $this->releaseStart($this->input->getArgument('releaseName'));
                break;
            case self::ARG_FINISH:
                $this->releaseFinish($this->input->getArgument('releaseName'));
                break;
            case self::ARG_RETRIEVE:
                $this->releaseRetrieve($this->input->getArgument('releaseName'));
                break;
            case self::ARG_PUBLISH:
                $this->releasePublish($this->input->getArgument('releaseName'));
                break;
        }
    }

    private function releaseStart($releaseName)
    {
        $this->log(sprintf('Start release %s', $releaseName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CREATE_BRANCH, self::BRANCH_RELEASE . $releaseName, self::BRANCH_DEVELOP));
    }

    private function releasePublish($releaseName)
    {
        $this->log(sprintf('Publish release %s', $releaseName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_RELEASE . $releaseName, null));
        $this->git->git(sprintf(self::CMD_PUSH_BRANCH, self::BRANCH_RELEASE . $releaseName));
    }

    private function releaseRetrieve($releaseName)
    {
        $this->log(sprintf('Retrieve release %s', $releaseName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_RELEASE . $releaseName));
        $this->git->git(sprintf(self::CMD_PULL_BRANCH, self::BRANCH_RELEASE . $releaseName));
    }

    private function releaseFinish($releaseName)
    {
        $this->log(sprintf('Finish release %s', $releaseName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_MASTER, null));
        $this->git->git(sprintf(self::CMD_MERGE_BRANCH, self::BRANCH_RELEASE . $releaseName));
        $this->git->git(sprintf(self::CMD_TAG_ANNOT_BRANCH, $releaseName));
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_DEVELOP, null));
        $this->git->git(sprintf(self::CMD_MERGE_BRANCH, self::BRANCH_RELEASE . $releaseName));
        $this->git->git(sprintf(self::CMD_DELETE_BRANCH, self::BRANCH_RELEASE . $releaseName));
    }
}
