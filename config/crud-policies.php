<?php

return [

    /**
     * The url prefix of Omen file manager
     ** default is 'crud-policies'
     *! The url prefix must not match any file in public folder
     */
    'urlPrefix' => 'crud-policies',

    /**
     * The public path where are stored
     * crud-policies asset files
     *
     * Change this if you want to modify
     * published assets folder under /public
     ** default is 'vendor/crud-policies'
     */
    'assetPath' => 'vendor/crud-policies',

    /**
     * This is the package layout that will be used
     * to display crud elements
     *
     * You can publish views to know what sections it
     * has to display
     */
    'viewLayout' => 'crud-policies::crud.layout',

    // Dont touch this
    'isLoaded' => true
];
