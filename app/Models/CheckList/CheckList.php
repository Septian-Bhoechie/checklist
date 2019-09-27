<?php

namespace Bhoechie\Checklist\Models\Template;

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

    protected $casts = [
        'completed_at' => 'date',
        'due' => 'date',
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
}
