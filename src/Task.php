<?php

namespace Rezvani\Todopkg;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    public function label()
    {
        return $this->belongsTo('Rezvani\Todopkg\Label');
    }
}
