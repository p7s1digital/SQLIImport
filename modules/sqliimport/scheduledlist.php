<?php
/**
 * SQLi Import scheduled list view
 * @copyright Copyright (C) 2010 - SQLi Agency. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Jerome Vieilledent
 * @version @@@VERSION@@@
 * @package sqliimport
 */

/** @var eZModule $Module */
$Module = $Params['Module'];
$Result = [];
$tpl    = SQLIImportUtils::templateInit();

try {
    // Offset for pagination
    $offset      = isset($Params['UserParameters']['offset']) ? (int)$Params['UserParameters']['offset'] : 0;
    $limit       = eZPreferences::value('sqliimport_import_limit');
    $limit       = $limit ? $limit : 20; // Default limit is 20
    $imports     = SQLIScheduledImport::fetchList($offset, $limit, [], ['is_active' => 'desc', 'next' => 'asc']);
    $importCount = SQLIScheduledImport::count(SQLIScheduledImport::definition());
    $currentURI  = '/' . $Module->currentModule() . '/' . $Module->currentView();

    $tpl->setVariable('imports', $imports);
    $tpl->setVariable('offset', $offset);
    $tpl->setVariable('limit', $limit);
    $tpl->setVariable('uri', $currentURI);
    $tpl->setVariable('import_count', $importCount);
    $tpl->setVariable('view_parameters', $Params['UserParameters']);
} catch (Exception $e) {
    $errMsg = $e->getMessage();
    SQLIImportLogger::writeError($errMsg);
    $tpl->setVariable('error_message', $errMsg);
}

$Result['path'] = [
    [
        'url'  => false,
        'text' => SQLIImportUtils::translate('extension/sqliimport', 'Scheduled import list')
    ]
];
$Result['left_menu'] = 'design:sqliimport/parts/leftmenu.tpl';
$Result['content']   = $tpl->fetch('design:sqliimport/scheduledlist.tpl');
