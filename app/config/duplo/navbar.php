<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu structure
    'items' => [

        // This is a menu item
        'menu_hem'  => [
            'text'  => 'Hem',
            'url'   => $this->di->get('url')->create(''),
            'title' => 'Förstasidan'
        ],

	// Comments
	'menu_questions' => [
	    'text' => 'Frågor',
	    'url'  => $this->di->get('url')->create('questions'),
	    'title'=> 'Ställ, läs och svara på frågor.',
	],

	//Theme
	'menu_tags' => [
	    'text' => 'Taggar',
	    'url' => $this->di->get('url')->create('tags'),
	    'title' => 'Användare',
	],
 
        // This is a menu item
        'menu_users'  => [
            'text'  => 'Användare',
            'url'   => $this->di->get('url')->create('users'),
            'title' => 'Alla Duplos redovisningar',
        ],

	// This is a menu item
        'menu_about'  => [
            'text'  => 'Om MoppeTrim',
            'url'   => $this->di->get('url')->create('about'),
            'title' => 'Information om denna webbplats',
        ],

        // This is a menu item
        'menu_source' => [
            'text'  =>'Visa källkod',
            'url'   => $this->di->get('url')->create('showsource'),
            'title' => 'Visa källkoden för denna webbplats',
            'mark-if-parent-of' => 'menu_source',
        ],
    ],
 


    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getCurrentUrl(false)) {
            return true;
        }
    },



    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



   /**
     * Callback to create the url, if needed, else comment out.
     *
     */
   /*
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
    */
];
