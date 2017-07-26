<?php

class Ota extends BaseModel {
    public $table = 'ota_versions';

    function scopeLatest($query) {
        return $query->orderBy('version', 'DESC');
    }

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

    function fancyPlatform() {
        switch($this->platform) {
            case 'ios':
                return 'iOS';
                break;
            case 'android':
                return 'Android';
                break;
            case 'windows':
                return 'Windows';
                break;
            default:
                return $this->platform;
                break;
        }
    }

    public function getBuildStringAttribute()
    {
        return $this->version . ' (' . date('d/m/Y H:i', strtotime($this->created_at)) . ')';
    }

    function downloadUrl($file = 'manifest') {

        $url = secure_url(route('ota.download.deliver', array($this->platform, $this->environment, $this->version, $file)));

        if ($file == 'manifest' and $this->platform == 'ios') {
            return 'itms-services://?action=download-manifest&url=' . $url;
        } else {
            return $url;
        }
    }

    function filePath($type) {
        switch($type) {
            case 'app':
                $type = 'app.ipa';
                break;
            case 'profile':
                $type = 'profile.mobileprovision';
                break;
            case 'manifest':
                if ($this->platform == 'windows') {
                    $type = 'app.xap';
                } else {
                    $type = 'tmp/app.plist';
                }
                break;
            case 'certificate':
                $type = 'certificate.aetx';
                break;
            default:
                return false;
                break;
        }

        if ($this->platform == 'android') {
            $type = 'app.apk';
        }

        return base_path() . '/resources/ota/' . $this->platform . '/' . $this->environment . '/' . $this->version . '/' . $type;
    }
}
