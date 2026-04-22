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
            try {
                // Ensure the branding directory exists and is writable
                if (!Storage::disk('public')->exists('branding')) {
                    Storage::disk('public')->makeDirectory('branding');
                }

                $path = $data['logo_img']->store('branding', 'public');
                
                if ($path) {
                    Setting::set('app_logo', 'storage/' . $path);
                } else {
                    \Illuminate\Support\Facades\Log::error('Branding logo upload failed: File could not be stored.');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Branding logo upload exception: ' . $e->getMessage());
            }
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
