<?php

namespace Rezvani\Todopkg\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rezvani\Todopkg\TaskHandler;
use Rezvani\Todopkg\LabelHandler;

class MainController extends Controller
{
    public function __construct(TaskHandler $task, LabelHandler $label)
    {
        $this->middleware('auth:api');
        $this->task = $task;
        $this->label = $label;
    }

    public function addTask(Request $request)
    {
        $task = $this->task->create($request->all());
        if ($task) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    public function addLabel(Request $request)
    {
        $label = $this->label->create($request->all());
        if ($label) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    public function addLabelToTask(Request $request)
    {
        $task = $this->task->addLabelsToTask($request->all());
        if ($task) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    public function editTask(Request $request)
    {
        $task = $this->task->editTask($request->all());
        if ($task) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    public function editTaskStatus(Request $request)
    {
        $task = $this->task->editTaskStatus($request->all());
        if ($task) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    public function getAllLabels()
    {
        $labels = $this->label->getAll();
        return $labels;
    }

    public function getTaskByLabel(Request $request)
    {
        $task = $this->task->getTaskByLabel($request->all());
        return $task;
    }

    public function getTaskById(Request $request)
    {
        $task = $this->task->getTaskById($request->all());
        return $task;
    }
}
