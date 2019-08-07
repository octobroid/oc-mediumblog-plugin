<?php namespace Octobro\MediumBlog;

use Backend;
use Event;
use Octobro\MediumBlog\Models\Settings as MediumSettings;
use RainLab\Blog\Controllers\Posts as RainLabPostsController;
use System\Classes\PluginBase;

/**
 * mediumBlog Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['RainLab.Blog'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'mediumBlog',
            'description' => 'No description provided yet...',
            'author'      => 'octobro',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        RainLabPostsController::extendListFilterScopes(function($filter) {
            $filter->addScopes([
                'medium_blog' => [
                    'label'      => 'Medium Post',
                    'type'       => 'checkbox',
                    'conditions' => "source_by = 'medium'"
                ]
            ]);
        });
    }


    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Medium Blog',
                'description' => 'Manage available your medium feed.',
                'category'    => 'Blog',
                'icon'        => 'icon-medium',
                'class'       => 'Octobro\MediumBlog\Models\Settings',
                'order'       => 500,
                'keywords'    => 'Medium Blog'
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        $medium_link = 'https://medium.com/feed/'.MediumSettings::get('username');

        $schedule->call(function () use($medium_link){
            Queue::push('Octobro\MediumBlog\Jobs\FetchPosts', ['link' => $medium_link]);
        })->daily();
    }
}