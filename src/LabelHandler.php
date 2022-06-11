<?php

namespace Rezvani\Todopkg;

use Illuminate\Support\Facades\Auth;
use Rezvani\Todopkg\Label;
use Rezvani\Todopkg\Task;

class LabelHandler
{
    public function create($data)
    {
        $task = new Label;
        $task->name = $data['name'];
        $task->user_id = Auth::id();
        $task->save();
        return $task;
    }

    public function getAll()
    {
        $labels = Label::select('id', 'name')->get();
        $tasks = Task::where('user_id', Auth::id())->get();

        foreach ($labels as $key => $label) {
            $relatedTasks = [];

            foreach ($tasks as $key2 => $task) {
                $taskLabels = explode(",", $task->labels);
                if (in_array($label->id, $taskLabels)) {
                    array_push($relatedTasks, $task);
                }
            }

            $label->tasks = array_unique($relatedTasks);
            unset($relatedTasks);
            $relatedTasks = array();
        }

        return $labels;
    }
}
