<?php

$init = function ($bootstrap) {
    try {
        $db = Zend_Db_Table::getDefaultAdapter();
    } catch (Exception $e) {
        return;
    }

    try {
        $columns = $db->describeTable('aerosalloyalty_campaigns');
    } catch (Exception $e) {
        return;
    }

    if (isset($columns['prizes']) && !isset($columns['prizes_to_redeem'])) {
        try {
            $db->query('ALTER TABLE `aerosalloyalty_campaigns` CHANGE `prizes` `prizes_to_redeem` VARCHAR(255) NULL DEFAULT NULL');
        } catch (Exception $e) {
            // Ignore migration failure to avoid blocking init; admin can rerun manually.
        }
    }
};
