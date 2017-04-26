<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace YiiExternal\Console;

use YiiExternal\Exception\InvalidArgumentException;
use YiiExternal\Exception\Exception;

class Command extends \CConsoleCommand
{
    public $daemon;

    protected function getPidPath()
    {
        return \Yii::app()->getRuntimePath() . '/pid';
    }

    protected function getPidfile($name)
    {
        return $this->getPidPath() . '/' . static::class .'_'. $name . '.pid';
    }

    protected function createPidPath()
    {
        if (! is_dir($this->getPidPath())) {
            mkdir($this->getPidPath());
            chmod($this->getPidPath(), 0777);
        }
    }

    public function run($args)
    {
        $flag = false;
        $taskName = $args[0];
        foreach ($args as $v) {
            if (strpos($v, '--daemon') !== false) {
                $flag = true;
                $this->createPidPath();
                list(, $action) = explode('=', $v);
                switch ($action) {
                    case 'start':
                        if (is_file($this->getPidfile($taskName))) {
                            printf("%s:%s process exists." . PHP_EOL, static::class, $taskName);
                        } else {
                            $pid = pcntl_fork();
                            if ($pid == -1) {
                                throw new Exception('Pcntl fork was wrong.');
                            } else if ($pid) {
                                // parent
                            } else {
                                file_put_contents($this->getPidfile($taskName), getmypid());
                                parent::run($args);
                            }
                        }
                        break;
                    case 'stop':
                        if ($pid = @file_get_contents($this->getPidfile($taskName))) {
                            posix_kill($pid, 9);
                            unlink($this->getPidfile($taskName));
                        }
                        break;
                    default:
                        throw new InvalidArgumentException('Daemon argument was wrong.');
                        break;
                }
                break;
            }
        }
        if (! $flag) {
            parent::run($args);
        }
    }
}