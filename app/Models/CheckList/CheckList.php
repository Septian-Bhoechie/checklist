<?php

namespace Bhoechie\Checklist\Models\CheckList;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * CheckList Model.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class CheckList extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'object_domain', 'object_id', 'description', 'is_completed', 'completed_at', 'urgency', 'due',
        'created_by', 'updated_by',
    ];

    // protected $dateFormat = 'Y-m-d\TH:i:sP';

    protected $casts = [
        'completed_at' => 'date',
        'due' => 'date',
        'is_completed' => 'boolean',
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
     * Define `hasMany` relation with TemplateItem model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(CheckListItem::class, 'checklist_id', 'id');
    }

    public function getAttributesAttribute()
    {
        return [
            'object_id' => $this->object_id,
            'object_domain' => $this->object_domain,
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

    public function setDueAttribute($value)
    {
        $this->attributes['due'] = (new Carbon($value))->format('Y-m-d H:i:s');
    }
}
