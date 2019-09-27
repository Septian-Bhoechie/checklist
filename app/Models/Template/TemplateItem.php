<?php

namespace Bhoechie\Checklist\Models\Template;

use Illuminate\Database\Eloquent\Model;

/**
 * Template Item Model.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class TemplateItem extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'template_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'urgency', 'description', 'due_interval', 'due_unit', 'template_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'template_id',
    ];

    /**
     * Define `belongsTo` relation with Template model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
