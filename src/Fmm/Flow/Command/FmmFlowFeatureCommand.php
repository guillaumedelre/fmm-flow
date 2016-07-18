<?php

namespace Fmm\Flow\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FmmFlowFeatureCommand extends AbstractFmmFlowCommand
{
    const ARG_START = 'start';
    const ARG_FINISH = 'finish';
    const ARG_PUBLISH = 'publish';
    const ARG_RETRIEVE = 'retrieve';

    protected function configure()
    {
        $this
            ->setName('feature')
            ->setDescription('Perform an action (start|publish|retrieve|finish) with a feature branch')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'Action to perform (start|publish|retrieve|finish).'
            )
            ->addArgument(
                'featureName',
                InputArgument::REQUIRED,
                'Name of the feature branch.'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        switch ($this->input->getArgument('action')) {
            case self::ARG_START:
                $this->featureStart($this->input->getArgument('featureName'));
                break;
            case self::ARG_FINISH:
                $this->featureFinish($this->input->getArgument('featureName'));
                break;
            case self::ARG_RETRIEVE:
                $this->featureRetrieve($this->input->getArgument('featureName'));
                break;
            case self::ARG_PUBLISH:
                $this->featurePublish($this->input->getArgument('featureName'));
                break;
        }
    }

    /**
     * @param $featureName
     */
    private function featureStart($featureName)
    {
        $this->log(sprintf('Start feature %s', $featureName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CREATE_BRANCH, self::BRANCH_FEATURE . $featureName, self::BRANCH_DEVELOP));
    }

    /**
     * @param $featureName
     */
    private function featurePublish($featureName)
    {
        $this->log(sprintf('Publish feature %s', $featureName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_FEATURE . $featureName, null));
        $this->git->git(sprintf(self::CMD_PUSH_BRANCH, self::BRANCH_FEATURE . $featureName));
    }

    /**
     * @param $featureName
     */
    private function featureRetrieve($featureName)
    {
        $this->log(sprintf('Retrieve feature %s', $featureName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_FEATURE . $featureName));
        $this->git->git(sprintf(self::CMD_PULL_BRANCH, self::BRANCH_FEATURE . $featureName));
    }

    /**
     * @param $featureName
     */
    private function featureFinish($featureName)
    {
        $this->log(sprintf('Finish feature %s', $featureName), self::LOG_LEVEL_COMMENT);
        $this->git->git(sprintf(self::CMD_CHECKOUT_BRANCH, self::BRANCH_DEVELOP, null));
        $this->git->git(sprintf(self::CMD_MERGE_BRANCH, self::BRANCH_FEATURE . $featureName));
        $this->git->git(sprintf(self::CMD_DELETE_BRANCH, self::BRANCH_FEATURE . $featureName));
    }
}
