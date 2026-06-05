<?php

namespace Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Skills\BrandingSkill;

class BrandingController extends Controller
{
    protected BrandingSkill $brandingSkill;

    public function __construct(BrandingSkill $brandingSkill)
    {
        $this->brandingSkill = $brandingSkill;
    }

    /**
     * Show branding settings form.
     */
    public function index()
    {
        $settings = $this->brandingSkill->getSettings();
        return view('admin.branding.index', compact('settings'));
    }

    /**
     * Update branding settings.
     */
    public function update(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Reached BrandingController@update');
        $request->validate([
            'app_name' => 'required|string|max:100',
            'logo_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $this->brandingSkill->updateBranding($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Branding settings updated successfully.',
                'settings' => $this->brandingSkill->getSettings()
            ]);
        }

        return back()->with('success', 'Branding settings updated successfully.');
    }

    /**
     * Programmatically create the storage link.
     */
    public function fixStorageLink()
    {
        try {
            Artisan::call('storage:link');
            $output = Artisan::output();
            
            return back()->with('success', 'Storage link fixed: ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fix storage link: ' . $e->getMessage());
        }
    }
}
