<?php

class Permission{

    private static function config()
    {
        return Config::get('permissions');
    }

    public static function tree($existing, $collections = null) {
        // We only go 3 levels deep
        $html = '<ul class="permissions">';

        // Top level items
        foreach(self::config() as $title => $permission) {
            $html .= "<li><h3>" . $title . "</h3>";

            if (isset($permission['children'])) {
                $html .= "<ul>";

                foreach($permission['children'] as $subTitle => $subPermission) {
                    $html .= "<li><h4>" . $subTitle . "</h4>";

                        if (isset($subPermission['children'])) {
                            $html .= "<ul>";

                            foreach($subPermission['children'] as $childTitle => $childPermission) {

                                $html .= "<li><label class='checkbox inline'>" . 
                                            Form::checkbox('permissions[' . $childPermission['value'] . ']', 1, $existing->hasAccess($childPermission['value'])) . $childTitle
                                         . "</label></li>";

                            }

                            $html .= "</ul>";
                        }

                    $html .= "</li>";
                }

                $html .= "</ul>";
            }


            $html .= "</li>";
        }

        $html .= "<li><h3>Collections</h3>";

        if ($collections) {

            $html .= "<ul>";

            foreach($collections as $collection) {

                $html .= "<li><h4>" . $collection->name . "</h4><ul>";

                    $html .= "<li><h5>Collection Permissions</h5></li>";

                    $html .= "<li><label class='checkbox inline'>" . 
                                            Form::checkbox('permissions[cms.collections.' . $collection->id . '.hierarchy-management]', 1, $existing->hasAccess('cms.collections.' . $collection->id . '.hierarchy-management'))
                                         . "Hierarchy Management</label></li>";

                    foreach($collection->nodetypes as $nodetype) {

                        $html .= "<li><h5>" . $nodetype->label . "</h5></li>";

                        foreach ($nodetype->columns as $column) {
                                
                            $html .= "<li><label class='checkbox inline'>" . 
                                            Form::checkbox('permissions[cms.collections.' . $collection->id . '.columns.' . $column->name . ']', 1, $existing->hasAccess('cms.collections.' . $collection->id . '.' . $column->name)) . $column->label
                                         . "</label></li>";

                        }

                    }

                $html .= "</ul></li>";

            }

            $html .= "</ul>";

        }

        $html .= "</li>";

        return $html . '</ul>';
    }

}