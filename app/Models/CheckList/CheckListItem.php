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
    protected $visible = [
        'id', 'attributes', 'type', 'links',
    ];

    protected $appends = [
        'attributes', 'type', 'links',
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

    public function getAttributesAttribute()
    {
        return [
            'assignee_id' => $this->object_id,
            'task_id' => $this->object_domain,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'urgency' => $this->urgency,
            'due' => $this->due,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }

    public function getTypeAttribute()
    {
        return 'checklists';
    }

    public function getLinksAttribute()
    {
        return [
            'self' => route('api.checklist.show', $this->id),
        ];
    }
}
