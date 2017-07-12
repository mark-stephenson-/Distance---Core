<?php

class Setting extends BaseModel {
    protected $fillable = array('name', 'value', 'user_id');
    
    public static function getConfig($value, $user_id = false)
    {
        $query = Setting::whereName($value);

        if ( $user_id ) {
            $query->whereUserId($user_id);
        }

        $query = $query->first();

        if ( ! $query ) {
            return '';
        }

        return $query->value;
    }

    public static function updateConfig($name, $value) {
        $query = Setting::whereName($name)->first();

        if ( ! $query ) {
            Setting::create( array('name' => $name, 'value' => $value) );
            return true;
        }

        $query->value = $value;
        $query->save();
        return true;
    }
}