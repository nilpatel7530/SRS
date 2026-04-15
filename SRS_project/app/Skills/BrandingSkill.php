<?php

namespace App\Skills;

use Modules\Admin\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandingSkill
{
    /**
     * Update branding settings.
     */
    public function updateBranding(array $data)
    {
        if (isset($data['app_name'])) {
            Setting::set('app_name', $data['app_name']);
        }

        if (isset($data['logo_img']) && $data['logo_img'] instanceof UploadedFile) {
            $path = $data['logo_img']->store('branding', 'public');
            Setting::set('app_logo', 'storage/' . $path);
        }
    }

    /**
     * Get current branding settings.
     */
    public function getSettings()
    {
        return [
            'app_name' => Setting::get('app_name', config('app.name', 'SRS')),
            'app_logo' => Setting::get('app_logo', 'vendor/adminlte/dist/img/AdminLTELogo.png'),
        ];
    }
}
