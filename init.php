<?php



$init = function($bootstrap) {

    # Backoffice activation menu

    Siberian_Module::addMenu(

        'Migachat',

        'migachat',

        'Migachat',

        'migachat/backoffice_migachat',

        'fa fa-align-justify');

};