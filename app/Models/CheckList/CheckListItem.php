<?php

namespace Bhoechie\Checklist\Models\CheckList;

use Illuminate\Database\Eloquent\Model;

/**
 * CheckList Item Model.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class CheckListItem extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assignee_id', 'task_id', 'description', 'is_completed', 'completed_at', 'urgency', 'due',
        'created_by', 'updated_by', 'checklist_id',
    ];

    protected $casts = [
        'completed_at' => 'date',
        'due' => 'date',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'checklist_id',
    ];

    /**
     * Define `belongsTo` relation with CheckList model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checklist()
    {
        return $this->belongsTo(CheckList::class, 'checklist_id', 'id');
    }
}
