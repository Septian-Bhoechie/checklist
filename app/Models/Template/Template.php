<?php

namespace Bhoechie\Checklist\Models\Template;

use Illuminate\Database\Eloquent\Model;

/**
 * Template Model.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class Template extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'due_interval', 'due_unit',
    ];

    /**
     * Define `belongsTo` relation with TemplateItem model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(TemplateItem::class, 'template_id', 'id');
    }
}
