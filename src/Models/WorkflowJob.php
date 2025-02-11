<?php declare(strict_types=1);

namespace Sassnowski\Venture\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 * @property ?Carbon $finished_at
 * @property ?Carbon $failed_at
 */
class WorkflowJob extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $dates = [
        'failed_at',
        'finished_at',
    ];

    public function __construct($attributes = [])
    {
        $this->table = config('venture.jobs_table');
        parent::__construct($attributes);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }
}
