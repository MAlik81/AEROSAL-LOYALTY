<?php
# Migachat module un-installer.
$name = 'Migachat';

# Clean-up library icons
Siberian_Feature::removeIcons($name);
Siberian_Feature::removeIcons($name . '-flat');

# Clean-up Layouts
$layoutData = [1];
$slug = 'migachat';

Siberian_Feature::removeLayouts($option->getId(), $slug, $layoutData);

# Clean-up Option(s)/Feature(s)
$code = 'migachat';
Siberian_Feature::uninstallFeature($code);

# Clean-up DB be really carefull with this.
$tables = [
    'migachat_setting',
    
];
Siberian_Feature::dropTables($tables);

# Clean-up module
Siberian_Feature::uninstallModule($name);
