@extends('layouts.app')

@section('title', 'Montants - Échelles Tarifs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="fas fa-money-bill-wave me-2"></i>
                Montants - Échelles Tarifs
            </h1>
            <p class="text-muted mb-0">Gestion des montants de déplacement par échelle</p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-list me-2"></i>Liste des Tarifs par Échelle
            </h5>
        </div>
        <div class="card-body p-0">
            @if($tarifs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">ID</th>
                                <th class="px-3 py-3">Échelle</th>
                                <th class="px-3 py-3">Type</th>
                                <th class="px-3 py-3">Montant Déplacement (DH)</th>
                                <th class="px-3 py-3">Max Jours</th>
                                <th class="px-3 py-3">Date de Création</th>
                                <th class="px-3 py-3">Date de Mise à Jour</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tarifs as $tarif)
                                <tr>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-secondary">#{{ $tarif->id }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="fw-semibold">{{ $tarif->echelle->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($tarif->type_in_out_mission == 'in')
                                            <span class="badge bg-info">Interne</span>
                                        @else
                                            <span class="badge bg-secondary">Externe</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="fw-semibold text-success" id="montant-{{ $tarif->id }}">{{ number_format($tarif->montant_deplacement, 2, ',', ' ') }} DH</span>
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-2" onclick="editMontant({{ $tarif->id }}, {{ $tarif->montant_deplacement }})" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-secondary" id="max-jours-{{ $tarif->id }}">{{ $tarif->max_jours ?? 'N/A' }} jours</span>
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-2" onclick="editMaxJours({{ $tarif->id }}, {{ $tarif->max_jours ?? 0 }})" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">{{ $tarif->created_at ? $tarif->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </td>
                                    <td class="px-3 py-3">
                                        <small class="text-muted">{{ $tarif->updated_at ? $tarif->updated_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun tarif trouvé</p>
                    <p class="text-muted small">Exécutez le seeder EchelleTarifSeeder pour créer les tarifs</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    @if($echelles->count() > 0)
    <div class="row g-3 mt-4">
        @foreach($echelles as $echelle)
            @php
                $echelleTarifs = $tarifs->where('echelle_id', $echelle->id);
                $tarifIn = $echelleTarifs->where('type_in_out_mission', 'in')->first();
                $tarifOut = $echelleTarifs->where('type_in_out_mission', 'out')->first();
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-layer-group me-2"></i>Échelle {{ $echelle->name }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Interne</small>
                            @if($tarifIn)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ number_format($tarifIn->montant_deplacement, 2, ',', ' ') }} DH</span>
                                    <small class="text-muted">Max: {{ $tarifIn->max_jours ?? 'N/A' }} jours</small>
                                </div>
                            @else
                                <span class="text-muted">Non défini</span>
                            @endif
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1">Externe</small>
                            @if($tarifOut)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ number_format($tarifOut->montant_deplacement, 2, ',', ' ') }} DH</span>
                                    <small class="text-muted">Max: {{ $tarifOut->max_jours ?? 'N/A' }} jours</small>
                                </div>
                            @else
                                <span class="text-muted">Non défini</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modifier le Tarif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit-tarif-id" name="tarif_id">
                    <div class="mb-3">
                        <label for="edit-montant" class="form-label">Montant Déplacement (DH)</label>
                        <input type="number" step="0.01" class="form-control" id="edit-montant" name="montant_deplacement" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-max-jours" class="form-label">Max Jours</label>
                        <input type="number" class="form-control" id="edit-max-jours" name="max_jours" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editMontant(tarifId, currentMontant) {
    document.getElementById('edit-tarif-id').value = tarifId;
    document.getElementById('edit-montant').value = currentMontant;
    const maxJoursText = document.getElementById('max-jours-' + tarifId).textContent;
    const maxJours = maxJoursText.replace(' jours', '').replace('N/A', '').trim();
    document.getElementById('edit-max-jours').value = maxJours || 0;
    document.getElementById('editForm').action = '{{ route("montants.update", ":id") }}'.replace(':id', tarifId);
    document.getElementById('editModalLabel').textContent = 'Modifier le Tarif';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function editMaxJours(tarifId, currentMaxJours) {
    document.getElementById('edit-tarif-id').value = tarifId;
    const montantText = document.getElementById('montant-' + tarifId).textContent;
    const montant = montantText.replace(' DH', '').replace(/\s/g, '').replace(',', '.').trim();
    document.getElementById('edit-montant').value = montant || 0;
    document.getElementById('edit-max-jours').value = currentMaxJours || 0;
    document.getElementById('editForm').action = '{{ route("montants.update", ":id") }}'.replace(':id', tarifId);
    document.getElementById('editModalLabel').textContent = 'Modifier le Tarif';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Handle form submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const tarifId = formData.get('tarif_id');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value;
    
    // Laravel requires POST with _method=PUT for PUT requests
    const data = {
        _method: 'PUT',
        montant_deplacement: parseFloat(formData.get('montant_deplacement')),
        max_jours: parseInt(formData.get('max_jours'))
    };
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Erreur lors de la mise à jour');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update the display
            const montant = parseFloat(formData.get('montant_deplacement'));
            const formattedMontant = montant.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('montant-' + tarifId).textContent = formattedMontant + ' DH';
            document.getElementById('max-jours-' + tarifId).textContent = formData.get('max_jours') + ' jours';
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = '<i class="fas fa-check-circle me-2"></i>Tarif mis à jour avec succès<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
            
            // Reload after a short delay to update summary cards
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('Erreur lors de la mise à jour: ' + (data.message || 'Erreur inconnue'));
        }
        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la mise à jour: ' + error.message);
        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
    });
});
</script>
@endpush
@endsection

