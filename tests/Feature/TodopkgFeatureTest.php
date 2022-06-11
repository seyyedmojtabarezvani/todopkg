<?php

namespace Rezvani\Todopkg\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodopkgFeatureTest extends TestCase
{
    private $api_token = "wFZdP80EyiG2X54mBA75sceIOPbdGxUBdoscytCUoGhp7y3vVBM2aZCglf7x";

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
            'labels' => 'tree,cloud,flower'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
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
            'name' => $this->generateRandomString(),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
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
            'task_id' => '7',
            'labels' => 'cloud, flower, droplet',
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
            'task_id' => '8',
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
            'task_id' => '9',
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
}
