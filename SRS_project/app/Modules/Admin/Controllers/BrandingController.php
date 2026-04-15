<?php

namespace Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $request->validate([
            'app_name' => 'required|string|max:100',
            'logo_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $this->brandingSkill->updateBranding($request->all());

        return back()->with('success', 'Branding settings updated successfully.');
    }
}
