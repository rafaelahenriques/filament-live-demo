<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'verdict', 'reason'])]
class Vote extends Model
{
    public const VERDICTS = ['yes' => 'Yes', 'maybe' => 'Maybe', 'no' => 'No'];
}
