<?php

namespace App\Models\Content;

use App\Enums\Status;
use App\Models\BaseModel;

class FaqCategory extends BaseModel
{
    protected $table = 'faq_categories';

    protected $guarded = ['id'];

    public function faqs()
    {
        return $this->hasMany('App\Models\Content\Faq', 'category_id', 'id')
            ->where('faqs.status', Status::ENABLE->value)
            ->orderBy('faqs.sort', 'desc')
            ->orderBy('faqs.id', 'desc');
    }
}
