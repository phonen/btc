<?php
/**
 * Created by PhpStorm.
 * User: 祥贵
 * Date: 2017/12/26
 * Time: 9:58
 */
namespace app\news\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Collect extends Command
{
    protected function configure()
    {
        $this->setName('test')->setDescription('Here is the remark ');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("TestCommand:");
    }
}