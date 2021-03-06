<?php

namespace V17Development\FlarumBlog;

// Laravel
use Illuminate\Events\Dispatcher;

// Flarum classes
use Flarum\Extend;
use Extend\Locales;
use Extend\Routes;
use Flarum\Discussion\Discussion;

// Controllers
use V17Development\FlarumBlog\Controller\BlogOverviewController;
use V17Development\FlarumBlog\Controller\BlogItemController;

// API controllers
use V17Development\FlarumBlog\Api\Controller\CreateBlogMetaController;
use V17Development\FlarumBlog\Api\Controller\UpdateBlogMetaController;

// Listeners
use V17Development\FlarumBlog\Listeners\AddDiscussionBlogMetaRelationship;
use V17Development\FlarumBlog\Listeners\FilterBlogArticles;
use V17Development\FlarumBlog\Listeners\ForumAttributesListener;
use V17Development\FlarumBlog\Listeners\CreateBlogMetaOnDiscussionCreate;

// Models
use V17Development\FlarumBlog\BlogMeta\BlogMeta;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__ . '/less/Forum.less')
        ->route('/blog', 'blog.overview', BlogOverviewController::class)
        ->route('/blog/category/{category}', 'blog.category', BlogOverviewController::class)
        ->route('/blog/{id:[\d\S]+(?:-[^/]*)?}', 'blog.post', BlogItemController::class)
        // Shall we add RSS?
        // ->get('/blog/rss.xml', 'blog.rss.xml', RSS::class)
    ,
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__ . '/less/Admin.less'),

    (new Extend\Routes('api'))
        ->post('/blogMeta', 'blog.meta', CreateBlogMetaController::class)
        ->patch('/blogMeta/{id}', 'blog.meta.edit', UpdateBlogMetaController::class),

    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\Model(Discussion::class))
        ->hasOne('blogMeta', BlogMeta::class, 'discussion_id'),

    new Extend\Compat(function (Dispatcher $events) {
        $events->subscribe(AddDiscussionBlogMetaRelationship::class);
        $events->subscribe(ForumAttributesListener::class);
        $events->subscribe(CreateBlogMetaOnDiscussionCreate::class);

        $events->subscribe(FilterBlogArticles::class);
    })
];
