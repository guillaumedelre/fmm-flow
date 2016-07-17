<?php
/**
 * Created by PhpStorm.
 * User: gdelre
 * Date: 16/07/16
 * Time: 21:50
 */

namespace Fmm\Flow\Command;


use GitWrapper\GitWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFmmFlowCommand extends Command
{
    /** @var InputInterface $input */
    protected $input;

    /** @var OutputInterface $output */
    protected $output;

    /** @var GitWrapper $git */
    protected $git;

    /** @var Logger $logger */
    protected $logger;

    /** @var array $options */
    protected $options;

    const LOG_LEVEL_NORMAL   = '%s';
    const LOG_LEVEL_INFO     = '<info>%s</info>';
    const LOG_LEVEL_COMMENT  = '<comment>%s</comment>';
    const LOG_LEVEL_QUESTION = '<question>%s</question>';
    const LOG_LEVEL_ERROR    = '<error>%s</error>';

    const CMD_CREATE_BRANCH = 'checkout -b %s %s';
    const CMD_CHECKOUT_BRANCH = 'checkout %s %s';
    const CMD_MERGE_BRANCH = 'merge --no-ff %s';
    const CMD_PUSH_BRANCH = 'push origin %s';
    const CMD_DELETE_BRANCH = 'branch -d %s';
    const CMD_PULL_BRANCH = 'pull --rebase origin %s';
    const CMD_TAG_ANNOT_BRANCH = 'tag -a %s';
    const CMD_TAG_BRANCH = 'tag %s';
    const CMD_PUSH_TAG = 'push origin --tags';

    const BRANCH_MASTER = 'master';
    const BRANCH_DEVELOP = 'develop';
    const BRANCH_FEATURE = 'feature/';
    const BRANCH_RELEASE = 'release/';
    const BRANCH_GH_PAGES = 'gh-pages';

    /**
     * AbstractFmmFlowCommand constructor.
     */
    public function __construct(array $options)
    {
        parent::__construct();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

        $this->git = new GitWrapper();

        // Stream output of subsequent Git commands in real time to STDOUT and STDERR.
        $this->git->streamOutput();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'git' => array(
                'binary'       => null,
                'privateKey'   => null,
            ),
        ));
    }

    /**
     * @param string $level
     * @param $message
     */
    public function log($message = '', $level = self::LOG_LEVEL_NORMAL)
    {
        $this->output->writeln(sprintf($level, $message));
    }

    /**
     * @param $start
     * @param null $end
     * @return float
     */
    protected function microtimeDiff($start, $end = null)
    {
        if (!$end) {
            $end = microtime();
        }
        list($startUsec, $startSec) = explode(" ", $start);
        list($endUsec, $endSec)     = explode(" ", $end);
        $diffSec                    = intval($endSec) - intval($startSec);
        $diffUsec                   = floatval($endUsec) - floatval($startUsec);
        return floatval($diffSec) + $diffUsec;
    }
}