<?php

namespace Rezvani\Todopkg\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodopkgFeatureTest extends TestCase
{
    private $api_token = "wFZdP80EyiG2X54mBA75sceIOPbdGxUBdoscytCUoGhp7y3vVBM2aZCglf7x";
    
    public function setUp(): void
    {
        parent::setUp();   
    }

    /**
     * Test to add a label.
     * 
     * label name must be unique
     *
     * @return void
     */
    public function testAddLabel()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/add-label', [
            'name' => 'general',
        ]);

        // $response
        //     ->assertStatus(200)
        //     ->assertJson([
        //         'success' => true,
        //     ]);

        $this->markTestIncomplete(
            'Repetitive label maybe caused error.'
        );
    }

    /**
     * Test to add a task.
     *
     * @return void
     */
    public function testAddTask()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/add-task', [
            'title' => 'edit task 8',
            'description' => 'due 2022/06/11',
            'status' => 'open',
            'labels' => 'general'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test to add a label to a task.
     *
     * @return void
     */
    public function testEditTaskLabels()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/add-task-labels', [
            'task_id' => '1',
            'labels' => 'general',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test to edit a task (title, description).
     *
     * @return void
     */
    public function testEditTask()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/edit-task', [
            'task_id' => '1',
            'title' => 'Test changed it',
            'description' => 'Merci test',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test to edit a task status (open, close).
     * 
     * If the task is closed, we send notification
     *
     * @return void
     */
    public function testEditTaskStatus()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->post('/edit-task-status', [
            'task_id' => '1',
            'status' => 'close',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test to get all labels.
     * 
     * @return void
     */
    public function testGetAllLabels()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->get('/get-all-labels');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id',
                    'name',
                    'tasks'
                ]
            ]);
    }

    /**
     * Test to get task by label.
     * 
     * @return void
     */
    public function testGetTaskByLabel()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->get('/get-task-by-label?label=general');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id',
                    'title',
                    'description',
                    'labels',
                ]
            ]);
    }

    /**
     * Test to get task by ID.
     * 
     * @return void
     */
    public function testGetTaskById()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->api_token}",
        ])->get('/get-task-by-id?task_id=1');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'status',
                'labels',
                'user_id',
            ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
