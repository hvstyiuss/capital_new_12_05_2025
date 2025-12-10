<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HRUserController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\EDocumentController;
use App\Http\Controllers\NoteAnnuelleController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\LeaveTrackingController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\TypeAnnonceController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\ParcoursController;
use App\Http\Controllers\EntiteController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\JoursFerieController;
use App\Http\Controllers\MutationController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DeplacementController;

// Health Check Routes
Route::get('/health', [HealthController::class, 'index'])->name('health');
Route::get('/health/detailed', [HealthController::class, 'detailed'])->name('health.detailed');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('auth.profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('auth.profile.update');
    
    // Account Settings Routes
    Route::prefix('account-settings')->name('account-settings.')->group(function () {
        Route::get('/', [AccountSettingsController::class, 'index'])->name('index');
        Route::post('/personal-info', [AccountSettingsController::class, 'updatePersonalInfo'])->name('update-personal-info');
        Route::post('/password', [AccountSettingsController::class, 'updatePassword'])->name('update-password');
        Route::post('/notifications', [AccountSettingsController::class, 'updateNotifications'])->name('update-notifications');
        Route::post('/profile-image', [AccountSettingsController::class, 'updateProfileImage'])->name('update-profile-image');
        Route::delete('/profile-image', [AccountSettingsController::class, 'deleteProfileImage'])->name('delete-profile-image');
    });
    
    // User Settings Routes
    Route::prefix('user-settings')->name('user-settings.')->group(function () {
        Route::get('/', [UserSettingController::class, 'index'])->name('index');
        Route::get('/edit', [UserSettingController::class, 'edit'])->name('edit');
        Route::put('/', [UserSettingController::class, 'update'])->name('update');
        Route::post('/ajax', [UserSettingController::class, 'updateAjax'])->name('update-ajax');
    });
    
    // Parcours Routes
    Route::prefix('parcours')->name('parcours.')->group(function () {
        Route::get('/index', [ParcoursController::class, 'myParcours'])->name('my');
        Route::get('/', [ParcoursController::class, 'index'])->name('index');
        Route::get('/{ppr}', [ParcoursController::class, 'show'])->name('show');
    });
    
    // Affectation Routes (Admin only)
    Route::middleware('role:admin')->prefix('affectation')->name('affectation.')->group(function () {
        Route::get('/', [AffectationController::class, 'index'])->name('index');
    });
    
    // Deplacement Routes for Chefs
    Route::middleware('auth')->prefix('deplacements/chef')->name('deplacements.chef.')->group(function () {
        Route::get('/', [DeplacementController::class, 'chefIndex'])->name('index');
    });
    
    // Deplacement Routes (Admin, Collaborateur Rh, super Collaborateur Rh, and Chefs for their entities)
    Route::middleware('auth')->prefix('deplacements')->name('deplacements.')->group(function () {
        // View routes (Admin, Collaborateur Rh, super Collaborateur Rh)
        Route::middleware('role:admin|Collaborateur Rh|super Collaborateur Rh')->group(function () {
            Route::get('/{type}', [DeplacementController::class, 'showByType'])->name('by-type')->where('type', 'central|regional');
            Route::get('/{type}/periode/{periode}', [DeplacementController::class, 'showByPeriod'])->name('by-period')->where('type', 'central|regional');
        });
        
        // Entity view (accessible by Admin, Collaborateur Rh, super Collaborateur Rh, or Chef of the entity)
        Route::get('/{type}/periode/{periode}/entite/{entite}', [DeplacementController::class, 'showByEntity'])->name('by-entity')->where('type', 'central|regional');
        
        // Preparation routes (Chef of entity, Admin, Collaborateur Rh, super Collaborateur Rh)
        Route::get('/{type}/periode/{periode}/entite/{entite}/preparer', [DeplacementController::class, 'preparerPeriode'])->name('preparer-periode')->where('type', 'central|regional');
        Route::get('/{type}/periode/{periode}/entite/{entite}/download-excel', [DeplacementController::class, 'downloadExcel'])->name('download-excel')->where('type', 'central|regional');
        Route::get('/{type}/periode/{periode}/entite/{entite}/start-process', [DeplacementController::class, 'startProcess'])->name('start-process')->where('type', 'central|regional');
        Route::post('/{type}/periode/{periode}/entite/{entite}/process-step1', [DeplacementController::class, 'processStep1'])->name('process-step1')->where('type', 'central|regional');
        Route::get('/{type}/periode/{periode}/entite/{entite}/process-step2', [DeplacementController::class, 'processStep2'])->name('process-step2')->where('type', 'central|regional');
        Route::post('/{type}/periode/{periode}/entite/{entite}/finalize', [DeplacementController::class, 'finalizeProcess'])->name('finalize')->where('type', 'central|regional');
        
        // Stats route (Admin only)
        Route::middleware('role:admin')->get('/stats', [DeplacementController::class, 'stats'])->name('stats');
    });
    
    // Agents Routes (Chef and Admin)
    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/consulter', [AgentController::class, 'consulter'])->name('consulter');
        Route::get('/gerer-comptes', [AgentController::class, 'gererComptes'])->name('gerer-comptes');
        Route::patch('/{agent}/status', [AgentController::class, 'updateStatus'])->name('update-status');
        Route::put('/{agent}', [AgentController::class, 'update'])->name('update');
    });
    
    // Entities Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/entities', [EntiteController::class, 'index'])->name('entities.index');
        Route::put('/entities/{entite}', [EntiteController::class, 'update'])->name('entities.update');
        Route::get('/entities/{entite}/users', [EntiteController::class, 'getUsers'])->name('entities.users');
    });
    
    // Echelle Tarifs Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/montants', [\App\Http\Controllers\EchelleTarifController::class, 'index'])->name('montants.index');
        Route::put('/montants/{id}', [\App\Http\Controllers\EchelleTarifController::class, 'update'])->name('montants.update');
    });
    
    // HR Dashboard (personal dashboard for all roles)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // RH Statistics (admin, Collaborateur Rh, super Collaborateur Rh)
    Route::middleware('role:admin|Collaborateur Rh|super Collaborateur Rh')->get('/hr/stats', [DashboardController::class, 'rhStats'])->name('hr.stats');

    // JSON statistics endpoint (can be reused by RH Stats page or others)
    Route::get('/hr/statistics', [DashboardController::class, 'getStatistics'])->name('hr.statistics');
    Route::post('/hr/dashboard/dismiss-alert/{demandeId}', [DashboardController::class, 'dismissAlert'])->name('hr.dashboard.dismiss-alert');
    Route::post('/hr/dashboard/dismiss-mutation-alert/{mutationId}', [DashboardController::class, 'dismissMutationAlert'])->name('hr.dashboard.dismiss-mutation-alert');
    Route::post('/hr/dashboard/dismiss-super-rh-mutation-alert/{mutationId}', [DashboardController::class, 'dismissSuperRhMutationAlert'])->name('hr.dashboard.dismiss-super-rh-mutation-alert');

    // HR User Management Routes
    Route::prefix('hr/users')->name('hr.users.')->group(function () {
        // Routes accessible by Admin, Collaborateur Rh, and super Collaborateur Rh
        Route::middleware('role:admin|Collaborateur Rh|super Collaborateur Rh')->group(function () {
            Route::get('/', [HRUserController::class, 'index'])->name('index');
            Route::get('/create', [HRUserController::class, 'create'])->name('create');
            Route::post('/', [HRUserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [HRUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [HRUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [HRUserController::class, 'destroy'])->name('destroy');
        });
        
        // Transfer routes accessible by Admin, Collaborateur Rh, and super Collaborateur Rh
        Route::middleware('role:admin|Collaborateur Rh|super Collaborateur Rh')->group(function () {
            Route::get('/{user}/transfer', [HRUserController::class, 'showTransfer'])->name('transfer');
            Route::post('/{user}/transfer', [HRUserController::class, 'processTransfer'])->name('transfer.store');
            Route::post('/swap-chefs', [HRUserController::class, 'swapChefs'])->name('swap-chefs');
        });
        
        // Show route accessible by Admin and Collaborateur Rh (handled in controller)
        // Must be after /create to avoid route conflicts
        Route::get('/{user}', [HRUserController::class, 'show'])->name('show');
    });

    // HR Leave Management Routes
    Route::prefix('hr/leaves')->name('hr.leaves.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/annuel', [LeaveController::class, 'annuel'])->name('annuel');
        Route::get('/maladie', [LeaveController::class, 'indexMaladie'])->name('maladie');
        Route::get('/agents', [LeaveController::class, 'agentsDemandes'])->name('agents');
        Route::get('/agents-solde', [LeaveController::class, 'agentsSolde'])->name('agents-solde');
        Route::get('/controle-central', [LeaveController::class, 'controleCentral'])->name('controle-central');
        Route::get('/controle-regional', [LeaveController::class, 'controleRegional'])->name('controle-regional');
        Route::middleware('role:admin')->group(function () {
            Route::get('/central', [LeaveController::class, 'centralDemandes'])->name('central');
            Route::get('/regional', [LeaveController::class, 'regionalDemandes'])->name('regional');
            Route::get('/stats', [LeaveController::class, 'leavesStats'])->name('stats');
        });
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::get('/create-maladie', [LeaveController::class, 'createMaladie'])->name('create-maladie');
        Route::get('/create-maternite', [LeaveController::class, 'createMaternite'])->name('create-maternite');
        Route::post('/store-maladie', [LeaveController::class, 'storeMaladie'])->name('store-maladie');
        Route::post('/store-maternite', [LeaveController::class, 'storeMaternite'])->name('store-maternite');
        Route::get('/holidays', [LeaveController::class, 'getHolidays'])->name('holidays');
        Route::get('/declare-retour', [LeaveController::class, 'showDeclareRetour'])->name('declare-retour');
        Route::post('/declare-retour', [LeaveController::class, 'storeDeclareRetour'])->name('store-declare-retour');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{demande}', [LeaveController::class, 'show'])->name('show');
        Route::get('/{demande}/edit', [LeaveController::class, 'edit'])->name('edit');
        Route::put('/{demande}', [LeaveController::class, 'update'])->name('update');
        Route::delete('/{demande}', [LeaveController::class, 'destroy'])->name('destroy');
        Route::patch('/{demande}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::patch('/{demande}/reject', [LeaveController::class, 'reject'])->name('reject');
        Route::patch('/{demande}/approve-chef', [LeaveController::class, 'approveAsChef'])->name('approve-chef');
        Route::post('/{demande}/reject-chef', [LeaveController::class, 'rejectAsChef'])->name('reject-chef');
        Route::post('/avis-depart/{avisDepart}/validate', [LeaveController::class, 'validateAvisDepart'])->name('validate-avis-depart');
        Route::post('/avis-depart/{avisDepart}/reject', [LeaveController::class, 'rejectAvisDepart'])->name('reject-avis-depart');
        Route::post('/avis-retour/{avisRetour}/validate', [LeaveController::class, 'validateAvisRetour'])->name('validate-avis-retour');
        Route::put('/avis-retour/{avisRetour}/update-date-retour-effectif', [LeaveController::class, 'updateDateRetourEffectif'])->name('update-date-retour-effectif');
        Route::get('/avis-retour/{avisRetour}/download-explanation-pdf', [LeaveController::class, 'downloadExplanationPDF'])->name('download-explanation-pdf');
        Route::get('/avis-depart/{avisDepart}/download-pdf', [LeaveController::class, 'downloadAvisDepartPDF'])->name('download-avis-depart-pdf');
        Route::get('/avis-retour/{avisRetour}/download-pdf', [LeaveController::class, 'downloadAvisRetourPDF'])->name('download-avis-retour-pdf');
        Route::get('/user-info/{ppr}', [LeaveController::class, 'showUserInfo'])->name('user-info');
    });

    // Leave Tracking Routes
    Route::get('/leaves/tracking', [LeaveTrackingController::class, 'index'])->name('leaves.tracking');

    // Mutation Routes (API-style, using policies for authorization)
    Route::prefix('mutations')->name('mutations.')->group(function () {
        Route::get('/create', [MutationController::class, 'create'])->name('create');
        Route::delete('/{mutation}', [MutationController::class, 'destroy'])->name('destroy');
        
        Route::get('/tracking', [MutationController::class, 'tracking'])->name('tracking');
        
        Route::get('/agent-requests', [MutationController::class, 'agentRequests'])->name('agent-requests');
        
        Route::get('/super-rh/requests', [MutationController::class, 'superRhRequests'])->name('super-rh.requests');
        
        Route::get('/super-rh/destination-requests', [MutationController::class, 'superRhDestinationRequests'])->name('super-rh.destination-requests');
        
        Route::get('/super-rh/validate/{mutation}', [MutationController::class, 'showSuperRhValidation'])->name('super-rh.validate');
        
        // API endpoints (these will be used by frontend)
        Route::post('/{mutation}/approve', [MutationController::class, 'approve'])->name('approve');
        Route::post('/{mutation}/reject', [MutationController::class, 'reject'])->name('reject');
        Route::post('/{mutation}/super-rh/destination-approve', [MutationController::class, 'approveDestinationReception'])->name('super-rh.destination-approve');
        Route::post('/{mutation}/super-rh/destination-reject', [MutationController::class, 'rejectDestinationReception'])->name('super-rh.destination-reject');
        
        // Stats route (Admin only)
        Route::middleware('role:admin')->get('/stats', [MutationController::class, 'stats'])->name('stats');
    });


    // Roles Management Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::post('roles/{role}/add-user', [\App\Http\Controllers\RoleController::class, 'addUser'])->name('roles.add-user');
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
    });

    // Permissions Management Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
    });
    
    // E-Documents Routes
    Route::prefix('edocuments')->name('edocuments.')->group(function () {
        Route::get('/loi-5220', [EDocumentController::class, 'loi5220'])->name('loi5220');
        Route::get('/statut-personnel', [EDocumentController::class, 'statutPersonnel'])->name('statut-personnel');
        Route::get('/demande-integration', [EDocumentController::class, 'demandeIntegration'])->name('demande-integration');
        Route::get('/unite-gestion-territoriale', [EDocumentController::class, 'uniteGestionTerritoriale'])->name('unite-gestion-territoriale');
    });
    
    // Notes Annuelles Routes
    Route::get('/notes-annuelles', [NoteAnnuelleController::class, 'index'])->name('notes-annuelles.index');
    
    // Jours Fériés Routes (All authenticated users)
    Route::get('/jours-feries', [JoursFerieController::class, 'index'])->name('jours-feries.index');
    
    // Suggestions Routes
    Route::get('/suggestions', [SuggestionController::class, 'index'])->name('suggestions.index');
    Route::post('/suggestions', [SuggestionController::class, 'store'])->name('suggestions.store');
    
    // Annonces Routes - All authenticated users can view, admin can manage
    // IMPORTANT: Specific routes must come before parameterized routes
    Route::get('/annonces', [AnnonceController::class, 'index'])->name('annonces.index');
    
    // Type Annonces CRUD - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('type-annonces', TypeAnnonceController::class);
    });
    
    // Annonces CRUD - Admin only (must be before /annonces/{annonce} to avoid route conflicts)
    Route::middleware('role:admin')->group(function () {
        Route::get('/annonces/create', [AnnonceController::class, 'create'])->name('annonces.create');
        Route::post('/annonces', [AnnonceController::class, 'store'])->name('annonces.store');
        Route::get('/annonces/{annonce}/edit', [AnnonceController::class, 'edit'])->name('annonces.edit')
            ->where('annonce', '[0-9]+');
        Route::put('/annonces/{annonce}', [AnnonceController::class, 'update'])->name('annonces.update')
            ->where('annonce', '[0-9]+');
        Route::delete('/annonces/{annonce}', [AnnonceController::class, 'destroy'])->name('annonces.destroy')
            ->where('annonce', '[0-9]+');
    });
    
    // Show route - must come after create route to avoid conflicts
    // Add constraint to prevent 'create' from matching as an ID
    Route::get('/annonces/{annonce}', [AnnonceController::class, 'show'])->name('annonces.show')
        ->where('annonce', '[0-9]+');
    
    // Test AJAX Route
    Route::post('/test-ajax', function () {
        return response()->json([
            'success' => true,
            'message' => 'AJAX test successful',
            'timestamp' => now()
        ]);
    })->name('test.ajax');
    
    // Notifications Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/get', [NotificationController::class, 'get'])->name('get');
    });
});
