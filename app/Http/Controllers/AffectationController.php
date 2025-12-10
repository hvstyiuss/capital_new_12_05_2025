<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcours;
use App\Models\User;
use App\Models\Entite;
use Illuminate\Support\Facades\DB;

class AffectationController extends Controller
{
    /**
     * Display a listing of all transfers/affectations.
     * Shows parcours entries that were created through transfers (have created_by_ppr).
     */
    public function index(Request $request)
    {
        // Get all parcours entries that are affectations (transfers)
        // These are identified by having created_by_ppr not null (indicating a transfer was made)
        $query = Parcours::with(['user.userInfo', 'entite', 'grade', 'createdBy'])
            ->whereNotNull('created_by_ppr')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('fname', 'like', "%{$search}%")
                              ->orWhere('lname', 'like', "%{$search}%")
                              ->orWhere('ppr', 'like', "%{$search}%");
                })
                ->orWhereHas('entite', function($entiteQuery) use ($search) {
                    $entiteQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('poste', 'like', "%{$search}%")
                ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        // Filter by entity
        if ($request->filled('entite_id')) {
            $query->where('entite_id', $request->entite_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Paginate results
        $affectations = $query->paginate(20)->withQueryString();

        // Statistics
        $totalAffectations = Parcours::whereNotNull('created_by_ppr')->count();

        $thisMonthAffectations = Parcours::whereNotNull('created_by_ppr')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Get entities for filter
        $entites = Entite::orderBy('name')->get();

        return view('affectation.index', compact('affectations', 'totalAffectations', 'thisMonthAffectations', 'entites'));
    }
}






