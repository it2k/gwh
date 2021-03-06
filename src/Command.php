<?php
/**
 * Created by PhpStorm.
 * User: zyuskin_en
 * Date: 31.12.14
 * Time: 0:18
 */

namespace It2k\GWH;

/**
 * Class Command
 *
 * @package It2k\GWH
 */
class Command
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Logger
     */
    protected $logger;

    /**
     * @param string  $command command for execution
     * @param string  $path    path from execute command
     * @param \Logger $logger  logger
     */
    public function __construct($command, $path, $logger)
    {
        $this->command = $command;
        $this->path    = $path;
        $this->logger  = $logger;
    }

    /**
     * @return array
     */
    public function execute()
    {
        if (!chdir($this->path)) {
            $this->logger->error('Cannot change directory to ' . $this->path);

            return array('command' => $this->command, 'errorCode' => 1);
        } else {
            $this->logger->info('Execute command ' . $this->command . ' from ' . $this->path);
            exec($this->command, $out, $resultCode);

            if ($resultCode != 0) {
                $this->logger->error('Cannot execute command ' . $this->command . ' from ' . $this->path);
            }
        }

        return array(
            'command'   => $this->command,
            'errorCode' => $resultCode,
            'output'    => $out,
        );
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }
} 