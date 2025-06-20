<?php

return [
    'pagination' => [
        'per_page_max' => 1000,
    ],
    'color' => [
        'max' => 6,
    ],
    'admin_role' => [
        'name_max' => 50,
    ],
    'admin' => [
        'username_min' => 4,
        'username_max' => 20,
        'password_min' => 6,
        'password_max' => 12,
    ],
    'admin_nav' => [
        'path_max' => 150,
        'icon_max' => 50,
        'name_max' => 50,
        'route_max' => 255,
        'url_max' => 255,
    ],
    'modules' => [
        'name_max' => 50,
    ],
    'tag' => [
        'name_max' => 50,
    ],
    'notice' => [
        'title_max' => 50,
        'slug_max' => 100,
    ],
    'banner' => [
        'image_max' => 255,
        'image_app_max' => 255,
        'url_max' => 255,
    ],
    'page' => [
        'title_max' => 50,
        'slug_max' => 100,
        'summary_max' => 255,
    ],
    'article_category' => [
        'name_max' => 50,
        'slug_max' => 100,
        'path_max' => 200,
    ],
    'article' => [
        'title_max' => 50,
        'slug_max' => 100,
        'summary_max' => 255,
        'cover_max' => 255,
        'cover_app_max' => 255,
    ],
    'news_category' => [
        'name_max' => 50,
        'slug_max' => 100,
        'path_max' => 200,
    ],
    'news' => [
        'title_max' => 50,
        'slug_max' => 100,
        'summary_max' => 255,
        'cover_max' => 255,
        'cover_app_max' => 255,
    ],
    'faq_category' => [
        'name_max' => 50,
        'slug_max' => 100,
        'path_max' => 200,
    ],
    'faq' => [
        'question_max' => 255,
        'slug_max' => 100,
        'summary_max' => 255,
        'cover_max' => 255,
        'cover_app_max' => 255,
    ],
    'upload' => [
        'related_field_max' => 50,
    ],
    'upload_settings' => [
        'thumb_width_max' => 1500,
        'thumb_height_max' => 1500,
    ],

    'clean_unused_uploads' => [
        'minutes_max' => 10080, // (分鐘)7天
    ],
];
