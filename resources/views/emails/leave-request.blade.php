<x-mail::message>
# Nouvelle demande de congé

Bonjour {{ $chef->name }},

{{ $employee->name }} a soumis une nouvelle demande de congé qui nécessite votre validation.

**Détails de la demande :**
- **Collaborateur :** {{ $employee->name }} (PPR: {{ $employee->ppr }})
- **Date de départ :** {{ \Carbon\Carbon::parse($avisDepart->date_depart)->format('d/m/Y') }}
- **Date de retour :** {{ \Carbon\Carbon::parse($avisDepart->date_retour)->format('d/m/Y') }}
- **Nombre de jours :** {{ $avisDepart->nb_jours_demandes }} jour(s)
- **Date de dépôt :** {{ $demande->created_at->format('d/m/Y à H:i') }}

<x-mail::button :url="route('hr.leaves.agents')">
Consulter la demande
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
