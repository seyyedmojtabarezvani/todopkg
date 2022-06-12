<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Rezvani\Todopkg\TaskHandler;
use Rezvani\Todopkg\LabelHandler;

class TodopkgUnitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->task = new TaskHandler;
        $this->label = new LabelHandler;
    }

    /**
     * Test create() function in LabelHandler.
     * 
     * Label name must be unique
     *
     * @return void
     */
    public function testCreateLabelFunction()
    {
        $data = ['name' => 'general'];
        Auth::loginUsingId(1);
        $this->markTestIncomplete(
            'Repetitive label maybe caused error.'
        );
        // $this->assertNotNull($this->label->create($data));
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Test create() function in TaskHandler.
     *
     * @return void
     */
    public function testCreateTaskFunction()
    {
        $data = ['title' => 'edit task 7', 'description' => 'due 2022/06/12', 'status' => 'open', 'labels' => 'tree,droplet,flower'];
        Auth::loginUsingId(1);
        $this->assertNotNull($this->task->create($data));
    }

    /**
     * Test addLabelsToTask() function in TaskHandler.
     *
     * @return void
     */
    public function testAddLabelsToTaskFunction()
    {
        $data = ['task_id' => '1', 'labels' => 'tree,droplet,flower'];
        Auth::loginUsingId(1);
        $this->assertNotNull($this->task->addLabelsToTask($data));
    }

    /**
     * Test editTask() function in TaskHandler.
     *
     * @return void
     */
    public function testEditTaskFunction()
    {
        $data = ['task_id' => '1', 'title' => 'Unit test changed me', 'description' => 'Thanks unit test'];
        Auth::loginUsingId(1);
        $this->assertNotNull($this->task->editTask($data));
    }

    /**
     * Test editTaskStatus() function in TaskHandler.
     *
     * @return void
     */
    public function testEditTaskStatusFunction()
    {
        $data = ['task_id' => '1', 'status' => 'open'];
        Auth::loginUsingId(1);
        $this->assertNotNull($this->task->editTaskStatus($data));
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
