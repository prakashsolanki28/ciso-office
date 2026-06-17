<?php

namespace App\Http\Controllers\SOP;

use App\Http\Controllers\Controller;
use Modules\SOP\Models\Sop;

class PublicSopController extends Controller
{
    public function index()
    {
        $sops = Sop::where('is_public', true)
            ->latest()
            ->get();

        return view('sops.index', compact('sops'));
    }
}
