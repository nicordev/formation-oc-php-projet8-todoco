<?php

namespace App\Tests\unit\Entity;


use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetCreatedAt()
    {
        $createdAt = new \DateTime("1970-01-01 00:00:01");
        $task = (new Task())->setCreatedAt($createdAt);
        $result = $task->getCreatedAt();

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertEquals("1970-01-01 00:00:01", $result->format("Y-m-d H:i:s"));
    }

    public function testToggle()
    {
        $task = (new Task());
        $task->toggle(true);
        $this->assertTrue($task->isDone());

        $task->toggle(false);
        $this->assertFalse($task->isDone());
    }
}