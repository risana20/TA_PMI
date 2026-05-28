<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query  = Reimbursement::with('user')->latest();

        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $reimbursements = $query->paginate(10)->withQueryString();
        return view('pages.superadmin.acc-reimbursement.index', compact('reimbursements', 'search'));
    }

    public function validasi(Reimbursement $reimbursement)
    {
        $reimbursement->update([
            'status'       => 'DIVALIDASI',
            'tgl_validasi' => now()->toDateString(),
            'validated_by' => Auth::id(),
        ]);
        return back()->with('success', 'Reimbursement berhasil divalidasi.');
    }

    public function batalkan(Reimbursement $reimbursement)
    {
        $reimbursement->update([
            'status'       => 'DIBATALKAN',
            'tgl_validasi' => null,
            'validated_by' => null,
        ]);
        return back()->with('success', 'Reimbursement berhasil dibatalkan.');
    }
}
