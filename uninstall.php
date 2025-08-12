<?php
# Migastarter un-installer.
$name = 'Migastarter';
# Clean-up library icons
Siberian_FeatadvertsmoveIcons($name);
Siberian_Feature::removeIcons($name . '-flat');
# Clean-up Layouts
$layoutData = [1];
$slug = 'migadverts';
Siberian_Feature::removeLayouts($option->getId(), $slug, $layoutData);
# Clean-up Option(s)/Feature(s)
$code = 'migastarter';
Siberian_Feature::uninstallFeature($code);
# Clean-up DB be really carefull with this.
$tables = [
    // 'migastarter_setting',
    // 'migastarter_category',
    // 'migastarter_product'
];
Siberian_Feature::dropTables($tables);
# Clean-up module
Siberian_Feature::uninstallModule($name);