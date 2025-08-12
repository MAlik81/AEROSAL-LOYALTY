<?php

$init = function ($bootstrap) {
    # Backoffice activation menu
    Siberian_Module::addMenu(
        'Migastarter',
        'migastarter',
        'Migastarter',
        'migastarter/backoffice_migastarter',
        'fa fa-star'
    );
};
