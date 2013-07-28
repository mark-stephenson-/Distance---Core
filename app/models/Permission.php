<?php

class Permission{

    private static function config()
    {
        return Config::get('permissions');
    }

    public static function tree($existing) {
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

        return $html . '</ul>';
    }

}