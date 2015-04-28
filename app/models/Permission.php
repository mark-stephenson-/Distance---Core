<?php

Form::macro('permissionCheckbox', function($existingPermissions, $permission, $title, $wrapInLi = true, $attributes = array())
{
    $ret = "<label class='checkbox inline'>" . 
                                            Form::checkbox('permissions[' . $permission . ']', 1, $existingPermissions->hasAccess($permission), $attributes) . $title
                                         . "</label>";
    if ($wrapInLi) {
        $ret = "<li>$ret</li>";
    }

    return $ret;
});

Form::macro('selectAllCheckbox', function($connectId, $wrapInLi = true)
{
    $ret = "<label class='checkbox inline'>" . Form::checkbox('', 1, 0, array('class' => 'js-select-all', 'data-to-select-id' => $connectId)) . "Select All</label>";

    if ($wrapInLi) {
        $ret = "<li>$ret</li>";
    }

    return $ret;
});

class Permission{

    private static function config()
    {
        return Config::get('permissions');
    }

    public static function tree($existing, $apps = null) {
        // We only go 3 levels deep
        $html = '<ul class="permissions">';

        // Top level items
        foreach(self::config() as $title => $permission) {
            $html .= "<li class='title'><h3>" . $title . "</h3>";

            if (isset($permission['children'])) {
                $html .= "<ul>";

                foreach($permission['children'] as $subTitle => $subPermission) {
                    $html .= "<li class='title'><h4>" . $subTitle . "</h4>";

                        if (isset($subPermission['children'])) {
                            $html .= "<ul>";

                            foreach($subPermission['children'] as $childTitle => $childPermission) {

                                $html .= Form::permissionCheckbox($existing, $childPermission['value'], $childTitle);

                            }

                            $html .= "</ul>";
                        }

                    $html .= "</li>";
                }

                $html .= "</ul>";
            }


            $html .= "</li>";
        }

        $html .= "<li class='title'><h3>Apps</h3>";

        if ($apps) {
            
            $html .= "<ul>";

            foreach($apps as $app) {

                $html .= "<li><h4>App: " . $app->name . "</h4><ul>";

                $html .= "<h5>App Permissions</h5><ul>";

                $html .= "<li style='display: block; margin-bottom: 10px;'>";
                $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collection-management', 'Collection Management', false);
                $html .= "</li>";

                $html .= "</ul>";

                $html .= "<h5>App Distribution</h5><ul>";

                $html .= "<li style='display: block; margin-bottom: 10px;'>";
                $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.ota.create', 'Create', false);
                $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.ota.read', 'Read', false);
                $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.ota.update', 'Update', false);
                $html .= "</li>";

                $html .= "</ul>";

                foreach($app->collections as $collection) {

                    $html .= "<li class='title'><h5>Collection: " . $collection->name . "</h5><ul>";

                        $html .= "<li class='title'><h6>Collection Permissions</h6></li>";

                        $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.update', 'Update');
                        $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.delete', 'Delete');
                        $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.hierarchy-management', 'Hierarchy Management');

                        $html .= "<li class='title'><h6>Catalogue Permissions</h6></li>";

                        $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.catalogues.create', 'Create');
                        $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.catalogues.update', 'Update');
                        $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.catalogues.delete', 'Delete');

                        $html .= "<li class='title'><h6>Can Upload to:</h6></li>";


                        $selectId = uniqid();

                        $html .= Form::selectAllCheckbox($selectId);

                        foreach($collection->catalogues as $catalogue) {
                            $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.catalogues.' . $catalogue->id . '.upload', $catalogue->name, array('data-select-id' => $selectId));
                        }

                        foreach($collection->nodetypes as $nodetype) {

                            $html .= "<li class='title'><h6>Node Type: " . $nodetype->label . "</h6></li>";

                            // CRUD permissions
                            $html .= "<li style='display: block; margin-bottom: 10px;'>";
                            $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.' . $nodetype->name . '.create', 'Create', false);
                            $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.' . $nodetype->name . '.read', 'Read', false);
                            $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.' . $nodetype->name . '.update', 'Update', false);
                            $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.' . $nodetype->name . '.delete', 'Delete', false);
                            $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.' . $nodetype->name . '.revision-management', 'Revision Management', false);
                            $html .= "</li>";

                            $selectId = uniqid();
                            $html .= Form::selectAllCheckbox($selectId);

                            foreach ($nodetype->columns as $column) {                                
                                $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.' . $nodetype->name . '.columns.' . $column->name, $column->label, array('data-select-id' => $selectId));
                            }

                        }
                    
                        $html .= "<li class='title'><h6>Data Permissions</h6></li>";

                        $html .= Form::permissionCheckbox($existing, 'cms.apps.' . $app->id . '.collections.' . $collection->id . '.data.export', 'Export');

                    $html .= "</ul></li>";

                }

                $html .= "</ul></li>";

            }

            $html .= "</ul>";

        }

        $html .= "</li>";

        return $html . '</ul>';
    }

}