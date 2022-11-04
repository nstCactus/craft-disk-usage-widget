<?php

namespace nstcactus\craftcms\diskUsageWidget\services;

use craft\base\Component;
use mikehaertl\shellcommand\Command as ShellCommand;
use nstcactus\craftcms\diskUsageWidget\exceptions\ShellCommandException;

class ShellCommandsService extends Component
{
    public function executeShellCommand(string $command): string
    {
        $shellCommand = new ShellCommand();
        $shellCommand->setCommand($command);

        // If we don't have proc_open, maybe we've got exec
        if (!function_exists('proc_open') && function_exists('exec')) {
            $shellCommand->useExec = true;
        }

        $success = $shellCommand->execute();
        if (!$success) {
            throw new ShellCommandException("An error occurred while executing the <code>$command</code> command.");
        }

        return $shellCommand->getOutput();
    }
}
