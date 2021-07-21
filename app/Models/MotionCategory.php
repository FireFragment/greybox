<?php

namespace App\Models;

class MotionCategory extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function translation()
    {
        return $this->belongsTo(\App\Translation::class, 'name', 'id');
    }

    public function motions()
    {
        return $this->belongsToMany(Motion::class, 'motion_categories_motions', 'motion_category', 'motion');
    }

    /**
     * @return array
     */
    public function getMotionsWithTexts(): array
    {
        $motions = array();
        foreach ($this->motions()->get() as $motion) {
            $motion->text = $motion->textTranslation()->first();
            $motion->short_text = $motion->shortTextTranslation()->first();
            $motions[] = $motion;
        }
        return $motions;
    }
}