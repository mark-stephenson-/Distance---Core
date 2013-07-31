<?php

class Ota extends BaseModel {
    public $table = 'ota_versions';

    public function scopeiOS()
    {
        return $this->wherePlatform('ios');
    }

    public function scopeAndroid()
    {
        return $this->wherePlatform('android');
    }

    public function scopeProduction()
    {
        return $this->whereEnvironment('production');
    }

    public function scopeTesting()
    {
        return $this->whereEnvironment('testing');
    }

    public function scopeOrder()
    {
        return $this->orderBy('version', 'DESC');
    }
}