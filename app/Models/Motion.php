<?php

namespace App\Models;

class Motion extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'short_text', 'note'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function textTranslation()
    {
        return $this->belongsTo(\App\Translation::class, 'text', 'id');
    }

    public function shortTextTranslation()
    {
        return $this->belongsTo(\App\Translation::class, 'short_text', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(MotionCategory::class, 'motion_categories_motions', 'motion', 'motion_category');
    }

    /**
     * @return array
     */
    public function getCategoriesWithNames(): array
    {
        $categories = array();
        foreach ($this->categories()->get() as $category) {
            $category->name = $category->translation()->first();
            $categories[] = $category;
        }
        return $categories;
    }
}