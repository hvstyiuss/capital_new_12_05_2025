@extends('layouts.app')

@section('title', 'Tableau de Bord - Capital')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Tableau de Bord</h1>
            <p class="text-muted mb-0">Bienvenue, {{ $user->fname }} {{ $user->lname }}</p>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($isChef && isset($pendingDemandesCount) && $pendingDemandesCount > 0)
    <x-alert type="info" 
             title="Demandes en attente" 
             :dismissible="true"
             id="pendingDemandesAlert" 
             data-no-auto-hide="true" 
             data-alert-key="pending-demandes-alert"
             class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <p class="mb-0">Vous avez <strong>{{ $pendingDemandesCount }}</strong> demande(s) de congé en attente de traitement</p>
            <a href="{{ route('hr.leaves.agents', ['statut' => 'pending']) }}" 
               class="btn btn-info btn-sm"
               aria-label="Gérer les demandes en attente">
                <i class="fas fa-tasks me-1" aria-hidden="true"></i>
                Gérer les demandes
            </a>
        </div>
        @if($pendingDemandesForChef->count() > 0)
        <hr class="my-3">
        <div class="mt-3">
            @foreach($pendingDemandesForChef as $demande)
                @php
                    $avis = $demande->avis;
                    $avisDepart = $avis?->avisDepart;
                    $agentName = $demande->user ? ($demande->user->fname . ' ' . $demande->user->lname) : 'N/A';
                    $dateDebut = $avisDepart?->date_depart?->format('d/m/Y') ?? 'N/A';
                    $nbrJours = $avisDepart?->nb_jours_demandes ?? 0;
                @endphp
                <div class="bg-white rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $agentName }}</strong>
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar me-1" aria-hidden="true"></i>
                                Départ: {{ $dateDebut }} | 
                                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                {{ $nbrJours }} jour(s)
                            </small>
                        </div>
                        <a href="{{ route('hr.leaves.agents', ['statut' => 'pending']) }}" 
                           class="btn btn-sm btn-outline-info"
                           aria-label="Voir les détails de la demande">
                            <i class="fas fa-eye me-1" aria-hidden="true"></i>
                            Voir
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </x-alert>
    @endif

    <!-- Pending Mutations Alert for Chefs -->
    @if($isChef && isset($pendingMutationsCount) && $pendingMutationsCount > 0)
    <x-alert type="info" 
             title="Demandes de mutation en attente" 
             :dismissible="true"
             id="pendingMutationsAlert" 
             data-no-auto-hide="true" 
             data-alert-key="pending-mutations-alert"
             class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <p class="mb-0">Vous avez <strong>{{ $pendingMutationsCount }}</strong> demande(s) de mutation en attente de votre validation</p>
            <a href="{{ route('mutations.agent-requests', ['status' => 'pending']) }}" 
               class="btn btn-info btn-sm"
               aria-label="Gérer les demandes de mutation en attente">
                <i class="fas fa-tasks me-1" aria-hidden="true"></i>
                Gérer les demandes
            </a>
        </div>
        @if(isset($pendingMutationsForChef) && $pendingMutationsForChef->count() > 0)
        <hr class="my-3">
        <div class="mt-3">
            @foreach($pendingMutationsForChef as $mutation)
                @php
                    $agentName = $mutation->user ? ($mutation->user->fname . ' ' . $mutation->user->lname) : 'N/A';
                    $agentPpr = $mutation->user?->ppr ?? 'N/A';
                    $toEntiteName = $mutation->toEntite?->name ?? 'N/A';
                    $dateDepot = $mutation->created_at?->format('d/m/Y') ?? 'N/A';
                    $mutationType = ucfirst($mutation->mutation_type ?? '');
                @endphp
                <div class="bg-white rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $agentName }}</strong>
                            <small class="text-muted">({{ $agentPpr }})</small>
                            <small class="text-muted d-block">
                                <i class="fas fa-building me-1" aria-hidden="true"></i>
                                Destination: {{ $toEntiteName }} | 
                                <i class="fas fa-calendar me-1" aria-hidden="true"></i>
                                Date de dépôt: {{ $dateDepot }} | 
                                <i class="fas fa-tag me-1" aria-hidden="true"></i>
                                Type: {{ $mutationType }}
                            </small>
                        </div>
                        <a href="{{ route('mutations.agent-requests', ['status' => 'pending']) }}" 
                           class="btn btn-sm btn-outline-info"
                           aria-label="Voir les détails de la mutation">
                            <i class="fas fa-eye me-1" aria-hidden="true"></i>
                            Voir
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </x-alert>
    @endif

    @if(isset($needsReturnDeclaration) && $needsReturnDeclaration && $returnDeclarationDemande)
    <x-alert type="info" 
             title="Déclaration de retour" 
             :dismissible="false"
             class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="flex-grow-1">
                <p class="mb-1">Aujourd'hui est votre date de retour - Veuillez déclarer votre retour</p>
                @php
                    $avis = $returnDeclarationDemande->avis;
                    $avisDepart = $avis?->avisDepart;
                    $dateRetour = $avisDepart?->date_retour?->format('d/m/Y') ?? 'N/A';
                @endphp
                <small class="text-muted">Date de retour prévue: <strong>{{ $dateRetour }}</strong></small>
            </div>
            <a href="{{ route('hr.leaves.declare-retour') }}" 
               class="btn btn-info btn-sm"
               aria-label="Déclarer le retour de congé">
                <i class="fas fa-edit me-1" aria-hidden="true"></i>
                Déclarer le retour
            </a>
        </div>
    </x-alert>
    @endif

    @if(isset($recentStatusChanges) && count($recentStatusChanges) > 0)
        @foreach($recentStatusChanges as $statusChange)
            @php
                $demande = $statusChange['demande'];
                $status = $statusChange['status'];
                $isRecent = $statusChange['is_recent'];
                $avis = $demande->avis;
                $avisDepart = $avis ? $avis->avisDepart : null;
                $dateDebut = $avisDepart && $avisDepart->date_depart ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d/m/Y') : ($demande->date_debut ? \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') : 'N/A');
                $dateRetour = $avisDepart && $avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d/m/Y') : 'N/A';
                $nbrJours = $avisDepart ? ($avisDepart->nb_jours_demandes ?? 0) : 0;
            @endphp
            @if($status == 'approved')
            <div class="alert alert-success border-success mb-4 position-relative alert-dismissible show" id="alert-{{ $demande->id }}" role="alert" data-no-auto-hide="true" style="padding-right: 3.5rem;">
                <button type="button" onclick="dismissAlert({{ $demande->id }})" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" style="z-index: 10; cursor: pointer;"></button>
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Demande approuvée</h6>
                        <p class="mb-2">Votre demande de congé a été approuvée</p>
                        <div class="small">
                            <div><i class="fas fa-calendar me-1"></i><strong>Date de départ:</strong> {{ $dateDebut }}</div>
                            <div><i class="fas fa-calendar-check me-1"></i><strong>Date de retour:</strong> {{ $dateRetour }}</div>
                            <div><i class="fas fa-clock me-1"></i><strong>Nombre de jours:</strong> {{ $nbrJours }} jour(s)</div>
                        </div>
                    </div>
                    <a href="{{ route('leaves.tracking') }}" class="btn btn-success btn-sm ms-3">
                        <i class="fas fa-eye me-1"></i>Voir les détails
                    </a>
                </div>
            </div>
            @elseif($status == 'rejected')
            <div class="alert alert-danger border-danger mb-4 position-relative alert-dismissible show" id="alert-{{ $demande->id }}" role="alert" data-no-auto-hide="true" style="padding-right: 3.5rem;">
                <button type="button" onclick="dismissAlert({{ $demande->id }})" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" style="z-index: 10; cursor: pointer;"></button>
                <div class="d-flex align-items-center">
                    <i class="fas fa-times-circle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Demande rejetée</h6>
                        <p class="mb-2">Votre demande de congé a été rejetée</p>
                        <div class="small">
                            <div><i class="fas fa-calendar me-1"></i><strong>Date de départ:</strong> {{ $dateDebut }}</div>
                            <div><i class="fas fa-calendar-check me-1"></i><strong>Date de retour:</strong> {{ $dateRetour }}</div>
                            <div><i class="fas fa-clock me-1"></i><strong>Nombre de jours:</strong> {{ $nbrJours }} jour(s)</div>
                        </div>
                        @if($nbrJours > 0)
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>Les {{ $nbrJours }} jour(s) ont été remboursés à votre solde.
                        </small>
                        @endif
                    </div>
                    <a href="{{ route('leaves.tracking') }}" class="btn btn-danger btn-sm ms-3">
                        <i class="fas fa-eye me-1"></i>Voir les détails
                    </a>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    <!-- Avis de Retour Declaration Alerts (for Chefs) -->
    @if($isChef && isset($recentAvisRetourDeclarations) && count($recentAvisRetourDeclarations) > 0)
        @foreach($recentAvisRetourDeclarations as $avisRetourDeclaration)
            <div class="alert alert-info border-info mb-4 position-relative alert-dismissible show" id="avis-retour-alert-{{ $avisRetourDeclaration['demande']->id }}" role="alert" data-no-auto-hide="true" style="padding-right: 3.5rem;">
                <button type="button" onclick="dismissAvisRetourAlert({{ $avisRetourDeclaration['demande']->id }})" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" style="z-index: 10; cursor: pointer;"></button>
                <div class="d-flex align-items-center">
                    <i class="fas fa-plane-arrival me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Avis de Retour Déclaré</h6>
                        <p class="mb-2"><strong>{{ $avisRetourDeclaration['collaborateur_name'] }}</strong> a déclaré son avis de retour</p>
                        <div class="small">
                            <div><i class="fas fa-calendar me-1"></i><strong>Date de départ:</strong> {{ $avisRetourDeclaration['date_depart'] }}</div>
                            <div><i class="fas fa-calendar-check me-1"></i><strong>Date de retour déclarée:</strong> {{ $avisRetourDeclaration['date_retour_declaree'] }}</div>
                            <div><i class="fas fa-clock me-1"></i><strong>Nombre de jours consommés:</strong> {{ $avisRetourDeclaration['nbr_jours_consumes'] }} jour(s)</div>
                        </div>
                    </div>
                    <a href="{{ route('hr.leaves.agents') }}" class="btn btn-info btn-sm ms-3">
                        <i class="fas fa-eye me-1"></i>Voir les détails
                    </a>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Mutation Status Change Alerts -->
    @if(isset($recentMutationChanges) && count($recentMutationChanges) > 0)
        @foreach($recentMutationChanges as $mutationChange)
            @if($mutationChange['status'] == 'approved')
            <div class="alert alert-success border-success mb-4 position-relative alert-dismissible show" id="mutation-alert-{{ $mutationChange['mutation']->id }}" role="alert" data-no-auto-hide="true" style="box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); padding-right: 3.5rem;">
                <button type="button" onclick="dismissMutationAlert({{ $mutationChange['mutation']->id }})" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Fermer" style="z-index: 10; cursor: pointer;"></button>
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Demande de mutation approuvée</h6>
                        <p class="mb-2">Votre demande de mutation a été approuvée</p>
                        <div class="small">
                            <div><i class="fas fa-building me-1"></i><strong>Entité de destination:</strong> {{ $mutationChange['to_entite_name'] }}</div>
                            <div><i class="fas fa-calendar me-1"></i><strong>Date de dépôt:</strong> {{ $mutationChange['date_depot'] }}</div>
                            <div><i class="fas fa-tag me-1"></i><strong>Type:</strong> {{ ucfirst($mutationChange['mutation']->mutation_type) }}</div>
                            @if($mutationChange['mutation']->date_debut_affectation)
                            <div class="mt-2 p-2 bg-light rounded">
                                <i class="fas fa-calendar-check me-1 text-success"></i><strong>Date de début d'affectation:</strong> 
                                <span class="text-success fw-bold">{{ $mutationChange['mutation']->date_debut_affectation->format('d/m/Y') }}</span>
                                <br>
                                <small class="text-muted">Cette date a été ajoutée à votre parcours. Vous commencerez dans votre nouvelle entité à partir de cette date.</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('mutations.tracking') }}" class="btn btn-success btn-sm ms-3">
                        <i class="fas fa-eye me-1"></i>Voir les détails
                    </a>
                </div>
            </div>
            @elseif($mutationChange['status'] == 'rejected')
            <div class="alert alert-danger border-danger mb-4 position-relative alert-dismissible show" id="mutation-alert-{{ $mutationChange['mutation']->id }}" role="alert" data-no-auto-hide="true" style="box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); padding-right: 3.5rem;">
                <button type="button" onclick="dismissMutationAlert({{ $mutationChange['mutation']->id }})" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Fermer" style="z-index: 10; cursor: pointer;"></button>
                <div class="d-flex align-items-center">
                    <i class="fas fa-times-circle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Demande de mutation rejetée</h6>
                        <p class="mb-2">Votre demande de mutation a été rejetée</p>
                        <div class="small">
                            <div><i class="fas fa-building me-1"></i><strong>Entité de destination:</strong> {{ $mutationChange['to_entite_name'] }}</div>
                            <div><i class="fas fa-calendar me-1"></i><strong>Date de dépôt:</strong> {{ $mutationChange['date_depot'] }}</div>
                            <div><i class="fas fa-tag me-1"></i><strong>Type:</strong> {{ ucfirst($mutationChange['mutation']->mutation_type) }}</div>
                            @if($mutationChange['rejector_name'] ?? null)
                            <div><i class="fas fa-user-times me-1"></i><strong>Rejetée par:</strong> {{ $mutationChange['rejector_name'] }}</div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('mutations.tracking') }}" class="btn btn-danger btn-sm ms-3">
                        <i class="fas fa-eye me-1"></i>Voir les détails
                    </a>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    <!-- Super Collaborateur Rh - Pending Mutations Alerts -->
    @if(isset($pendingSuperCollaborateurRhMutations) && count($pendingSuperCollaborateurRhMutations) > 0)
        @foreach($pendingSuperCollaborateurRhMutations as $mutationData)
            <div class="alert alert-warning border-warning mb-4 position-relative alert-dismissible fade show" id="super-rh-mutation-alert-{{ $mutationData['mutation']->id }}" role="alert" data-no-auto-hide="true" style="box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); padding-right: 3.5rem;">
                <button type="button" onclick="dismissSuperRhMutationAlert({{ $mutationData['mutation']->id }})" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Fermer" style="z-index: 10; cursor: pointer;"></button>
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">
                            @if($mutationData['is_intermediate_review'])
                                Mutation en attente de révision intermédiaire
                            @else
                                Mutation en attente de validation finale
                            @endif
                        </h6>
                        <p class="mb-2">
                            @if($mutationData['is_intermediate_review'])
                                Une mutation externe approuvée par la direction actuelle nécessite votre décision : l'envoyer à la destination ou la rejeter.
                            @else
                                Une mutation complètement validée nécessite votre validation finale et la définition de la date de début d'affectation.
                            @endif
                        </p>
                        <div class="small">
                            <div><i class="fas fa-user me-1"></i><strong>Agent:</strong> {{ $userName }} (PPR: {{ $mutation->ppr }})</div>
                            <div><i class="fas fa-building me-1"></i><strong>Entité de destination:</strong> {{ $toEntiteName }}</div>
                            <div><i class="fas fa-calendar me-1"></i><strong>Date de dépôt:</strong> {{ $dateDepot }}</div>
                            <div><i class="fas fa-tag me-1"></i><strong>Type:</strong> {{ ucfirst($mutation->mutation_type) }}</div>
                        </div>
                    </div>
                    <a href="{{ route('mutations.super-rh.validate', $mutation->id) }}" class="btn btn-warning btn-sm ms-3">
                        <i class="fas fa-check-circle me-1"></i>
                        @if($isIntermediateReview)
                            Réviser
                        @else
                            Valider et définir la date
                        @endif
                    </a>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Personal Information Card -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-gradient-primary text-white border-0 py-4">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="fas fa-user fs-4"></i>
                    </div>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Informations Personnelles</h5>
                    <small class="opacity-75">Vos informations administratives</small>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Profile Picture Section -->
                <div class="col-lg-3 text-center">
                    <div class="position-relative d-inline-block">
                        @if($user->userInfo && $user->userInfo->photo)
                            <img src="{{ $user->userInfo->photo_url }}" 
                                 alt="{{ $user->name }}" 
                                 id="dashboard_profile_image"
                                 class="rounded-circle shadow-lg" 
                                 style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        @else
                            <div class="rounded-circle bg-gradient-primary d-inline-flex align-items-center justify-content-center shadow-lg" 
                                 id="dashboard_profile_image_placeholder"
                                 style="width: 150px; height: 150px; border: 5px solid #fff;">
                                <span class="text-white fw-bold" style="font-size: 3.5rem;">
                                    {{ strtoupper(substr($user->fname ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->lname ?? '', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <!-- Upload Button Overlay -->
                        <div class="position-absolute bottom-0 end-0">
                            <label for="dashboard_image_upload" class="btn btn-sm btn-primary rounded-circle p-2 shadow-lg" style="cursor: pointer; width: 40px; height: 40px;" title="Changer la photo">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" 
                                   id="dashboard_image_upload" 
                                   name="image" 
                                   accept="image/jpeg,image/png,image/jpg"
                                   class="d-none"
                                   onchange="uploadDashboardImage(this)">
                        </div>
                    </div>
                    <h4 class="mt-3 mb-1 fw-bold text-dark">{{ $user->fname }} {{ $user->lname }}</h4>
                    <small class="text-muted">Cliquez sur l'icône caméra pour changer votre photo</small>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-id-card me-1"></i>{{ $user->ppr }}
                    </p>
                </div>
                
                <!-- Information Cards -->
                <div class="col-lg-9">
                    
                    <!-- Section: Informations d'Identification -->
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">
                            <i class="fas fa-id-card me-2"></i>Informations d'Identification
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-primary bg-opacity-10 text-primary">
                                            <i class="fas fa-id-badge"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Matricule DDP</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $user->ppr }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-fingerprint"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">CINE</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $user->userInfo->cin ?? 'Non fourni' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-user-tag"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Nom complet</label>
                                            <p class="fw-bold mb-0 text-dark">{{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-secondary bg-opacity-10 text-secondary">
                                            <i class="fas fa-venus-mars"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Sexe</label>
                                            <p class="fw-bold mb-0 text-dark">Masculin</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Informations Professionnelles -->
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">
                            <i class="fas fa-briefcase me-2"></i>Informations Professionnelles
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-danger bg-opacity-10 text-danger">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Grade</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $user->userInfo && $user->userInfo->grade ? strtoupper($user->userInfo->grade->name) : 'Non défini' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-purple bg-opacity-10 text-purple d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 12px;">
                                            <i class="fas fa-layer-group fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Echelle</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $user->userInfo && $user->userInfo->grade && $user->userInfo->grade->Echelle ? $user->userInfo->grade->Echelle->name : 'Non définie' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-card">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-users-cog"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Corps fonctionnel</label>
                                            @if($user->userInfo && $user->userInfo->corps)
                                                <p class="fw-bold mb-1 text-dark">
                                                    {{ $user->userInfo->corps === 'forestier' ? 'Personnel Forestier' : 'Personnel de Support' }}
                                                </p>
                                                <small class="text-muted">{{ $corpsDescriptions[$user->userInfo->corps] ?? '' }}</small>
                                            @else
                                                <p class="fw-bold mb-0 text-muted">Non défini</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Affectation -->
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>Affectation
                        </h6>
                        <div class="row g-3">
                        @if($shouldShowDirection)
                        <div class="col-md-6">
                            <div class="info-card h-100">
                                <div class="d-flex align-items-start">
                                    <div class="info-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-sitemap"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <label class="text-muted small mb-1 d-block">Direction</label>
                                        <p class="fw-bold mb-0 text-dark">{{ $directionName }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-city"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Ville d'affectation</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $villeAffectation }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Entité d'affectation</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $currentEntite ? $currentEntite->name : 'Non définie' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-secondary bg-opacity-10 text-secondary">
                                            <i class="fas fa-user-cog"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Fonction</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $currentParcours && $currentParcours->poste ? $currentParcours->poste : 'Collaborateur' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Chef</label>
                                            @if($chefName && $chefPpr)
                                                <p class="fw-bold mb-0 text-dark">{{ $chefName }}</p>
                                                @if($chefEntiteName)
                                                    <small class="text-muted d-block mt-1">{{ $chefEntiteName }}</small>
                                                @endif
                                            @else
                                                <p class="fw-bold mb-0 text-muted">Non défini</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Contact -->
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">
                            <i class="fas fa-address-book me-2"></i>Contact
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Email</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $user->userInfo && $user->userInfo->email ? $user->userInfo->email : $user->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">GSM</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $user->userInfo->gsm ?? 'Non fourni' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-card">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">Adresse</label>
                                            <p class="fw-bold mb-0 text-dark">{{ $user->userInfo->adresse ?? 'Non fournie' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Informations Financières -->
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">
                            <i class="fas fa-university me-2"></i>Informations Financières
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="info-card">
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1 d-block">RIB</label>
                                            <p class="fw-bold mb-0 text-dark font-monospace">
                                                {{ $user->userInfo->rib ?? 'Non fourni' }}
                                                @if($user->userInfo && $user->userInfo->rib)
                                                    <button onclick="copyToClipboard('{{ $user->userInfo->rib }}', event)" 
                                                            class="btn btn-sm btn-link text-info p-0 ms-2" 
                                                            title="Copier le RIB">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-3">
                <i class="fas fa-bolt text-warning me-2"></i>Actions Rapides
            </h5>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('hr.leaves.create') }}'">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3">
                        <i class="fas fa-calendar-plus text-primary fs-2"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Nouvelle Demande</h6>
                    <p class="text-muted small mb-0">Créer une nouvelle demande de congé</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('leaves.tracking') }}'">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3">
                        <i class="fas fa-list-alt text-success fs-2"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Mes Demandes</h6>
                    <p class="text-muted small mb-0">Suivre mes demandes de congé</p>
                </div>
            </div>
        </div>
        @if($isChef)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('hr.leaves.agents') }}'">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3">
                        <i class="fas fa-users-cog text-warning fs-2"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Mes Agents</h6>
                    <p class="text-muted small mb-0">Gérer les demandes de mes agents</p>
                </div>
            </div>
        </div>
        @else
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition-all cursor-pointer" onclick="window.location.href='{{ route('annonces.index') }}'">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle p-4 d-inline-flex mb-3">
                        <i class="fas fa-bullhorn text-info fs-2"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Annonces</h6>
                    <p class="text-muted small mb-0">Consulter les annonces</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}

.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.cursor-pointer {
    cursor: pointer;
}

.info-card {
    background: #f8f9fa;
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.info-card:hover {
    background: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.info-icon {
    width: 45px;
    height: 45px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.text-purple {
    color: #6f42c1 !important;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

/* Mutation alert styling - make them prominent and fixed */
[id^="mutation-alert-"] {
    border-left: 4px solid;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Ensure close button is visible and clickable */
.alert-dismissible .btn-close {
    padding: 0.75rem;
    opacity: 0.8;
    background-color: rgba(0, 0, 0, 0.1);
    border-radius: 0.25rem;
    transition: all 0.2s ease;
    z-index: 10 !important;
    cursor: pointer !important;
    pointer-events: auto !important;
    position: absolute !important;
    top: 0.75rem !important;
    right: 0.75rem !important;
    margin: 0 !important;
}

/* Ensure alert content doesn't overlap with close button for status change alerts */
.alert-dismissible[id^="alert-"],
.alert-dismissible[id^="mutation-alert-"],
.alert-dismissible[id^="avis-retour-alert-"],
.alert-dismissible[id^="super-rh-mutation-alert-"] {
    padding-right: 3.5rem;
}

.alert-dismissible .btn-close:hover {
    opacity: 1;
    background-color: rgba(0, 0, 0, 0.2);
    transform: scale(1.1);
}

.alert-dismissible .btn-close:focus {
    opacity: 1;
    outline: 2px solid rgba(0, 0, 0, 0.3);
    outline-offset: 2px;
}

/* Make alerts more prominent */
.alert-success.alert-dismissible,
.alert-danger.alert-dismissible {
    border-width: 2px;
}
</style>
@endpush

@push('scripts')
<script>
function uploadDashboardImage(input) {
    if (!input.files || !input.files[0]) {
        return;
    }

    const file = input.files[0];
    const maxSize = 7 * 1024 * 1024; // 7 MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

    // Validate file
    if (file.size > maxSize) {
        alert('L\'image ne peut pas dépasser 7 Mo.');
        input.value = '';
        return;
    }

    if (!allowedTypes.includes(file.type)) {
        alert('Le format de l\'image doit être JPG, JPEG ou PNG.');
        input.value = '';
        return;
    }

    // Show loading state
    const uploadBtn = input.previousElementSibling;
    const originalHtml = uploadBtn.innerHTML;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    uploadBtn.disabled = true;

    // Create FormData
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '{{ csrf_token() }}');

    // Upload image
    fetch('{{ route("account-settings.update-profile-image") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update image preview
            const imgElement = document.getElementById('dashboard_profile_image');
            const placeholderElement = document.getElementById('dashboard_profile_image_placeholder');
            
            if (imgElement) {
                imgElement.src = data.image_url + '?t=' + new Date().getTime();
            } else if (placeholderElement) {
                placeholderElement.outerHTML = `<img src="${data.image_url}?t=${new Date().getTime()}" 
                    alt="{{ $user->name }}" 
                    id="dashboard_profile_image"
                    class="rounded-circle shadow-lg" 
                    style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">`;
            }
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
            alert.style.zIndex = '9999';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>Photo de profil mise à jour avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 3000);
        } else {
            alert(data.message || 'Erreur lors du téléchargement de l\'image.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors du téléchargement de l\'image. Veuillez réessayer.');
    })
    .finally(() => {
        // Reset button
        uploadBtn.innerHTML = originalHtml;
        uploadBtn.disabled = false;
        input.value = '';
    });
}
</script>
<script>
function copyToClipboard(text, event) {
    navigator.clipboard.writeText(text).then(function() {
        if (event && event.target) {
            const button = event.target.closest('button');
            if (button) {
                const icon = button.querySelector('i');
                if (icon) {
                    const originalClass = icon.className;
                    icon.className = 'fas fa-check text-success';
                    setTimeout(() => {
                        icon.className = originalClass;
                    }, 2000);
                }
            }
        }
    }).catch(function(err) {
        console.error('Failed to copy:', err);
    });
}

function dismissAlert(demandeId) {
    const alertElement = document.getElementById('alert-' + demandeId);
    if (!alertElement) return;
    
    const dismissButton = alertElement.querySelector('button');
    if (dismissButton) {
        dismissButton.disabled = true;
    }
    
    fetch(`/hr/dashboard/dismiss-alert/${demandeId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertElement.style.transition = 'opacity 0.3s ease-out';
            alertElement.style.opacity = '0';
            setTimeout(() => {
                alertElement.remove();
            }, 300);
        } else {
            if (dismissButton) {
                dismissButton.disabled = false;
            }
            alert('Erreur lors de la fermeture de l\'alerte. Veuillez réessayer.');
        }
    })
    .catch(error => {
        console.error('Error dismissing alert:', error);
        if (dismissButton) {
            dismissButton.disabled = false;
        }
        alert('Erreur lors de la fermeture de l\'alerte. Veuillez réessayer.');
    });
}

function dismissMutationAlert(mutationId) {
    const alertElement = document.getElementById('mutation-alert-' + mutationId);
    if (!alertElement) return;
    
    const dismissButton = alertElement.querySelector('button.btn-close');
    if (dismissButton) {
        dismissButton.disabled = true;
    }
    
    fetch(`/hr/dashboard/dismiss-mutation-alert/${mutationId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Fade out and remove the alert
            alertElement.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            alertElement.style.opacity = '0';
            alertElement.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alertElement.remove();
            }, 300);
        } else {
            // Restore button on error
            if (dismissButton) {
                dismissButton.disabled = false;
            }
            alert('Erreur lors de la fermeture de l\'alerte. Veuillez réessayer.');
        }
    })
    .catch(error => {
        console.error('Error dismissing mutation alert:', error);
        // Restore button on error
        if (dismissButton) {
            dismissButton.disabled = false;
        }
        alert('Erreur lors de la fermeture de l\'alerte. Veuillez réessayer.');
    });
}

function dismissSuperRhMutationAlert(mutationId) {
    const alertElement = document.getElementById('super-rh-mutation-alert-' + mutationId);
    if (!alertElement) return;
    
    const dismissButton = alertElement.querySelector('button.btn-close');
    if (dismissButton) {
        dismissButton.disabled = true;
    }
    
    fetch(`/hr/dashboard/dismiss-super-rh-mutation-alert/${mutationId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Fade out and remove the alert
            alertElement.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            alertElement.style.opacity = '0';
            alertElement.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alertElement.remove();
            }, 300);
        } else {
            // Restore button on error
            if (dismissButton) {
                dismissButton.disabled = false;
            }
            alert('Erreur lors de la fermeture de l\'alerte. Veuillez réessayer.');
        }
    })
    .catch(error => {
        console.error('Error dismissing super RH mutation alert:', error);
        // Restore button on error
        if (dismissButton) {
            dismissButton.disabled = false;
        }
        alert('Erreur lors de la fermeture de l\'alerte. Veuillez réessayer.');
    });
}

// Handle persistent alert dismissal
document.addEventListener('DOMContentLoaded', function() {
    // Check and hide dismissed alerts on page load
    const alerts = document.querySelectorAll('[data-alert-key]');
    alerts.forEach(function(alert) {
        const alertKey = alert.getAttribute('data-alert-key');
        if (localStorage.getItem(alertKey) === 'dismissed') {
            alert.style.display = 'none';
        }
    });

    // Handle close button clicks
    alerts.forEach(function(alert) {
        const closeButton = alert.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                const alertKey = alert.getAttribute('data-alert-key');
                if (alertKey) {
                    localStorage.setItem(alertKey, 'dismissed');
                }
            });
        }
    });
});

// Dismiss avis de retour alert
function dismissAvisRetourAlert(demandeId) {
    const alertElement = document.getElementById('avis-retour-alert-' + demandeId);
    if (!alertElement) {
        return;
    }

    // Disable button to prevent double clicks
    const dismissButton = alertElement.querySelector('.btn-close');
    if (dismissButton) {
        dismissButton.disabled = true;
    }

    // Hide alert immediately for better UX (non-blocking)
    alertElement.style.transition = 'opacity 0.3s ease';
    alertElement.style.opacity = '0';
    
    // Try to save dismissal on server, but don't block if it fails
    fetch('{{ route("hr.dashboard.dismiss-avis-retour-alert", ":id") }}'.replace(':id', demandeId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Remove alert after fade out
            setTimeout(() => {
                if (alertElement && alertElement.parentNode) {
                    alertElement.remove();
                }
            }, 300);
        } else {
            // If server says no, still remove it locally but log the error
            console.warn('Server dismissed alert but returned non-success:', data);
            setTimeout(() => {
                if (alertElement && alertElement.parentNode) {
                    alertElement.remove();
                }
            }, 300);
        }
    })
    .catch(error => {
        // On error, still remove the alert locally (non-blocking)
        console.error('Error dismissing avis retour alert:', error);
        // Remove alert after fade out even on error
        setTimeout(() => {
            if (alertElement && alertElement.parentNode) {
                alertElement.remove();
            }
        }, 300);
    });
}

function uploadDashboardImage(input) {
    if (!input.files || !input.files[0]) {
        return;
    }

    const file = input.files[0];
    const maxSize = 7 * 1024 * 1024; // 7 MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

    // Validate file
    if (file.size > maxSize) {
        alert('L\'image ne peut pas dépasser 7 Mo.');
        input.value = '';
        return;
    }

    if (!allowedTypes.includes(file.type)) {
        alert('Le format de l\'image doit être JPG, JPEG ou PNG.');
        input.value = '';
        return;
    }

    // Show loading state
    const uploadBtn = input.previousElementSibling;
    const originalHtml = uploadBtn.innerHTML;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    uploadBtn.disabled = true;

    // Create FormData
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '{{ csrf_token() }}');

    // Upload image
    fetch('{{ route("account-settings.update-profile-image") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update image preview
            const imgElement = document.getElementById('dashboard_profile_image');
            const placeholderElement = document.getElementById('dashboard_profile_image_placeholder');
            
            if (imgElement) {
                imgElement.src = data.image_url + '?t=' + new Date().getTime();
            } else if (placeholderElement) {
                placeholderElement.outerHTML = `<img src="${data.image_url}?t=${new Date().getTime()}" 
                    alt="{{ $user->name }}" 
                    id="dashboard_profile_image"
                    class="rounded-circle shadow-lg" 
                    style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">`;
            }
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
            alert.style.zIndex = '9999';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>Photo de profil mise à jour avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 3000);
        } else {
            alert(data.message || 'Erreur lors du téléchargement de l\'image.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors du téléchargement de l\'image. Veuillez réessayer.');
    })
    .finally(() => {
        // Reset button
        uploadBtn.innerHTML = originalHtml;
        uploadBtn.disabled = false;
        input.value = '';
    });
}
</script>
@endpush
@endsection
