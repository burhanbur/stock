<?php

namespace App\Enums\Learning;

enum ProgressStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
