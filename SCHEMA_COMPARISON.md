# Database Schema Comparison: Image vs Current

## Summary of Changes Needed

### 1. **Demande Table** - NEEDS UPDATES
**Image Schema:**
- id
- statut
- user_id (foreign key to users.ppr)
- type (conge/mutation enum)
- entite_id (foreign key)
- created_by

**Current Schema:**
- id
- date_debut
- ppr (foreign key to users.ppr)
- statut (was removed in migration 2025_11_13_120000)
- timestamps

**Changes Needed:**
- Add: `type` enum('conge', 'mutation')
- Add: `entite_id` foreign key
- Add: `created_by` (ppr of creator)
- Keep: `ppr` (user_id) OR rename to `user_id`
- Restore: `statut` field (if needed)

### 2. **DemandeMutation Table** - NEW TABLE NEEDED
**Image Schema:**
- id
- demande_id (foreign key)
- entite_source_id
- entite_destination_id
- is_validated_envoi (boolean)
- decision_collaborateur_rh
- is_validated_reception (boolean)
- valide_par (ppr)
- poste_actuel
- poste_souhaite
- date_effet_souhaitee
- motif_demande

**Current:**
- `mutations` table exists but separate, not linked to `demandes`
- Has different fields

**Action:** Create new `demande_mutations` table

### 3. **DemandeConge Table** - NEW TABLE NEEDED
**Image Schema:**
- id
- demande_id (foreign key)
- type_conge_id (foreign key to type_conges)
- date_debut
- date_fin
- nbr_jours_demandes
- motif

**Current:**
- `conges` table exists but different structure (ppr, annee, etc.)
- Not linked to demandes

**Action:** Create new `demande_conges` table

### 4. **TypeConge Table** - NEW TABLE NEEDED
**Image Schema:**
- id
- name (values: annuel, maladie, exceptionnel)

**Current:**
- No such table exists

**Action:** Create `type_conges` table

### 5. **SoldeConge Table** - NEEDS UPDATES
**Image Schema:**
- id
- user_id (foreign key to users.ppr)
- solde_precedent
- solde_actuel
- solde_fixe
- annee
- solde_maladie
- solde_exceptionnel

**Current Schema:**
- id
- ppr (foreign key)
- solde_precedent
- solde_fix
- type (added in 2025_12_27)

**Changes Needed:**
- Add: `solde_actuel`
- Add: `annee`
- Add: `solde_maladie`
- Add: `solde_exceptionnel`
- Keep: `ppr` (or rename to user_id)

### 6. **CongeExceptionnel Table** - NEEDS UPDATES
**Image Schema:**
- id
- demande_conge_id (foreign key)
- type_exceptionnel_id (foreign key)

**Current:**
- `exceptionnels` table exists but different structure (motif, date_debut, date_fin)
- Not linked to demande_conges

**Action:** Update or create new structure

### 7. **CongeMaladie Table** - NEEDS UPDATES
**Image Schema:**
- id
- demande_conge_id (foreign key)
- type_maladie_id (foreign key)

**Current:**
- `maladies` table exists but is a lookup table (name, ordre)
- No link to demande_conges

**Action:** Create `conge_maladies` table

### 8. **TypeExceptionnel Table** - EXISTS BUT MAY NEED UPDATE
**Image Schema:**
- id
- name
- nbr_jours

**Current:**
- `type_exceps` table exists (id, name)
- Missing: `nbr_jours`

**Action:** Add `nbr_jours` field

### 9. **TypeMaladie Table** - NEW TABLE NEEDED
**Image Schema:**
- id
- name (values: cc, md, ld, m)

**Current:**
- `maladies` table exists but is different (name, ordre)
- Should be a lookup table for types

**Action:** Create `type_maladies` table OR update `maladies` table

### 10. **Avis Table** - EXISTS (mostly correct)
**Image Schema:**
- id
- demande_id (foreign key)
- date_depot

**Current:**
- id
- demande_id
- date_depot
- is_validated

**Status:** Mostly correct, keep as is

### 11. **AvisDepart & AvisRetour** - EXIST (mostly correct)
**Status:** These tables exist and match the schema mostly







