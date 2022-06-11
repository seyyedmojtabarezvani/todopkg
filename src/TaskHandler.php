<?php

namespace Mojtaba\Todopkg;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Mojtaba\Todopkg\Notifications\TaskClosedNotification;
use Mojtaba\Todopkg\Task;
use Mojtaba\Todopkg\Label;

class TaskHandler
{
    public function create($data)
    {
        $task = new Task;
        $task->title = $data['title'];
        $task->description = $data['description'];
        $task->status = $data['status'] === 'open' ? config('todopkg.task_status.open') : config('todopkg.task_status.close');
        $task->labels = $this->getLabelIDsFromNames($data['labels']) ?? null;
        // Alternatively if front-end sends label IDs directly
        // $task->labels = $this->formatLabelIDs($data['labels']) ?? null;
        $task->user_id = Auth::id();
        $task->save();
        return $task;
    }

    private function getLabelIDsFromNames($labels)
    {
        $labels = explode(",", $labels);
        $labelIDs = [];
        foreach ($labels as $key => $label) {

            $labelId = Label::select('id')->where('name', trim($label))->first();

            if ($labelId) {
                array_push($labelIDs, $labelId['id']);
            }
        }
        $uniqueLabelIDs = array_unique($labelIDs);

        return implode(",", $uniqueLabelIDs);
    }

    private function formatLabelIDs($labels)
    {
        $labels = explode(",", $labels);
        $labelIDs = [];

        // Checks if label id exists
        foreach ($labels as $key => $label) {

            $labelId = Label::select('id')->where('id', trim($label))->first();

            if ($labelId) {
                array_push($labelIDs, $labelId['id']);
            }
        }

        $uniqueLabelIDs = array_unique($labelIDs);

        return implode(",", $uniqueLabelIDs);
    }

    public function addLabelsToTask($data)
    {
        $task = Task::where('id', $data['task_id'])->first();

        if (!$task) {
            return null;
        }

        if (!$data['labels']) {
            return null;
        }

        // If with label names
        $newLabels = $this->getLabelIDsFromNames($data['labels']);
        // or with label IDs
        // $newLabels = $this->formatLabelIDs($data['labels']);

        $currentLabels = $task->labels ? $task->labels . "," : "";

        $allLabels = $currentLabels . $newLabels;

        $uniqueLabels = $this->getUniqueLabels($allLabels);

        $task->labels = $uniqueLabels;

        $task->save();

        return $task;
    }

    private function getUniqueLabels($labels)
    {
        $labels = explode(",", $labels);

        $uniqueLabelIDs = array_unique($labels);

        return implode(",", $uniqueLabelIDs);
    }

    public function editTask($data)
    {
        $task = Task::where('id', $data['task_id'])->first();

        if (!$task) {
            return null;
        }

        if (!$data['title']) {
            return null;
        }

        $task->title = $data['title'];

        $task->description = $data['description'];

        $task->save();

        return $task;
    }

    public function editTaskStatus($data)
    {
        $task = Task::where('id', $data['task_id'])->first();

        if (!$task) {
            return null;
        }

        if ($data['status'] !== 'open' && $data['status'] !== 'close') {
            return null;
        }

        $task->status = $data['status'] === 'open' ? config('todopkg.task_status.open') : config('todopkg.task_status.close');

        $task->save();

        // If task has been closed trigger a notification
        if (!$task->status) {
            $this->writeLog($task);
            $this->sendNotification($task);
        }

        return $task;
    }

    private function writeLog($task)
    {
        Log::info("The task '{$task->title}' has been closed by user id {$task->user_id}");
    }

    private function sendNotification($task)
    {
        $user = Auth::user();

        Notification::send($user, new TaskClosedNotification($task));
    }

    public function getTaskByLabel($data)
    {
        // If label name is given we have to find its label ID
        $label = Label::select('id')->where('name', $data['label'])->first();
        // If label ID is given
        // $label = Label::select('id')->where('id', $data->label)->get();
        // Label ID not found
        if (!$label) {
            return null;
        }

        $labelId = $label['id'];

        // I want two objects with no reference
        $tasks = Task::select('id', 'title', 'description', 'labels')->where('user_id', Auth::id())->get();
        $tsks = Task::select('id', 'title', 'description', 'labels')->where('user_id', Auth::id())->get();

        $relatedTasks = [];

        // For each task out of all tasks we wanna find the ones that matches our label
        foreach ($tasks as $key => $task) {
            $taskLabels = explode(",", $task->labels);

            // When matched
            if (in_array($labelId, $taskLabels)) {

                $relatedLbls = [];

                // We want to find other labels of the matched task
                foreach ($taskLabels as $key2 => $lblId) {

                    // Wanna find id and name of the label
                    $lbl = Label::select('id', 'name')->where('id', $lblId)->first();

                    $relatedLblTasks = [];

                    // Wanna find all tasks related to the label
                    foreach ($tsks as $key3 => $tsk) {
                        $tskLbls = explode(",", $tsk->labels);

                        if (in_array($lblId, $tskLbls)) {
                            array_push($relatedLblTasks, $tsk);
                        }
                    }

                    $lbl->tasks = array_unique($relatedLblTasks);
                    array_push($relatedLbls, $lbl);
                    unset($relatedLblTasks);
                    $relatedLblTasks = array();
                }

                $task->labels = $relatedLbls;

                unset($relatedLbls);
                $relatedLbls = array();

                array_push($relatedTasks, $task);
            }
        }

        return array_unique($relatedTasks);
    }

    public function getTaskById($data)
    {
        $task = Task::where(['id' => $data['task_id'], 'user_id' => Auth::id()])->first();

        if (!$task) {
            return null;
        }

        $task->labels = $this->getLabelNamesByIds($task->labels);
        return $task;
    }

    private function getLabelNamesByIds($labelIds)
    {
        $lblIdArray = explode(",", $labelIds);

        $lblNameArray = [];
        foreach ($lblIdArray as $key => $lblId) {
            $lbl = Label::select('name')->where('id', $lblId)->first();

            if (!$lbl) {
                continue;
            }

            $lblName = $lbl['name'];

            array_push($lblNameArray, $lblName);
        }

        return array_unique($lblNameArray);
    }
}
