<?php

class Ota extends BaseModel {
    public $table = 'ota_versions';

    public function scopeiOS($query)
    {
        return $query->wherePlatform('ios');
    }

    public function scopeAndroid($query)
    {
        return $query->wherePlatform('android');
    }

    public function scopeProduction($query)
    {
        return $query->whereEnvironment('production');
    }

    public function scopeTesting($query)
    {
        return $query->whereEnvironment('testing');
    }

    public function scopeOrder($query)
    {
        return $query->orderBy('version', 'DESC');
    }

    public function scopeCurrent($query)
    {
        $query = $query->order()->first();

        if ( ! $query ) {
            return false;
        }

        return $query;
    }

    public function getBuildStringAttribute()
    {
        return $this->version . ' (' . date('d/m/Y H:i', strtotime($this->created_at)) . ')';
    }
}