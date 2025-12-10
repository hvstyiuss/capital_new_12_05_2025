<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entite;
use App\Models\EntiteInfo;
use Illuminate\Support\Facades\DB;

class CurrentEntitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder contains the current entities and entite_infos from the database.
     * Generated on: 2025-11-21 11:47:07
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data (optional - comment out if you want to keep existing data)
        // EntiteInfo::truncate();
        // Entite::truncate();
        
        // Create entities
        $entitiesData = [
            [
                'id' => 4,
                'name' => 'DIRECTIONS REGIONALES ANEF - Tanger Tétouan Al Hoceima',
                'code' => '01-000',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 5,
                'name' => 'Parc National AL HOCEIMA',
                'code' => '01-001',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 6,
                'name' => 'Parc National TALASSEMTANE',
                'code' => '01-002',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 7,
                'name' => 'Direction Provinciale TANGER',
                'code' => '01-100',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 8,
                'name' => 'Direction Provinciale TETOUAN',
                'code' => '01-200',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 9,
                'name' => 'Direction Provinciale FAHS-ANJRA',
                'code' => '01-300',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 10,
                'name' => 'Direction Provinciale MDIQ-FNIDEQ',
                'code' => '01-400',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 11,
                'name' => 'Direction Provinciale LARACHE',
                'code' => '01-500',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 12,
                'name' => 'Direction Provinciale AL HOCEIMA',
                'code' => '01-600',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 13,
                'name' => 'Direction Provinciale CHEFCHAOUEN',
                'code' => '01-700',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 14,
                'name' => 'Direction Provinciale OUAZZANE',
                'code' => '01-800',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 15,
                'name' => 'Service administration & finance - Tanger',
                'code' => '01-B10',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 16,
                'name' => 'Service du génie forestier - Tanger',
                'code' => '01-B20',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 17,
                'name' => 'Service du domaine forestier et de la police des eaux et forets - Tanger',
                'code' => '01-B30',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 18,
                'name' => 'Service de l\'Animation et du Partenariat - Tanger',
                'code' => '01-B40',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 19,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Tanger',
                'code' => '01-B50',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 4,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 20,
                'name' => 'DIRECTIONS REGIONALES ANEF - Oriental',
                'code' => '02-000',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 21,
                'name' => 'Centre Technique de Suivi de la Désertification - Oriental',
                'code' => '02-001',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 22,
                'name' => 'Direction Provinciale OUJDA',
                'code' => '02-100',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 23,
                'name' => 'Direction Provinciale NADOR',
                'code' => '02-101',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 24,
                'name' => 'Direction Provinciale DRIOUCHE',
                'code' => '02-102',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 25,
                'name' => 'Direction Provinciale BERKANE',
                'code' => '02-103',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 26,
                'name' => 'Direction Provinciale JERADA',
                'code' => '02-104',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 27,
                'name' => 'Direction Provinciale GUERCHIF',
                'code' => '02-105',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 28,
                'name' => 'Direction Provinciale FIGUIG',
                'code' => '02-106',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 29,
                'name' => 'Direction Provinciale TAOURIRT',
                'code' => '02-107',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 30,
                'name' => 'Service administration & finance - Oriental',
                'code' => '02-B10',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 31,
                'name' => 'Service du génie forestier - Oriental',
                'code' => '02-B20',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 32,
                'name' => 'Service du domaine forestier et de la police des eaux et forets - Oriental',
                'code' => '02-B30',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 33,
                'name' => 'Service de l\'Animation et du Partenariat - Oriental',
                'code' => '02-B40',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 34,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Oriental',
                'code' => '02-B50',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 20,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 35,
                'name' => 'DIRECTIONS REGIONALES ANEF - Fès-Meknès',
                'code' => '03-000',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 36,
                'name' => 'Centre Technique de Développement des Ressources Cynégétiques',
                'code' => '03-0002',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 37,
                'name' => 'Parc National IFRANE',
                'code' => '03-001',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 38,
                'name' => 'Parc National TAZZEKKA',
                'code' => '03-002',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 39,
                'name' => 'Centre Technique de Développement des Ressources Cynégétiques',
                'code' => '03-003',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 40,
                'name' => 'Centre National d\'Hydrobiologie et de Pisciculture',
                'code' => '03-004',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 41,
                'name' => 'Centre Nationale d\'Hydrobiologie et de Pisciculture',
                'code' => '03-0041',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 42,
                'name' => 'Direction Provinciale MEKNES-ELHAJEB',
                'code' => '03-100',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 43,
                'name' => 'Direction Provinciale TAZA',
                'code' => '03-101',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 44,
                'name' => 'Direction Provinciale IFRANE',
                'code' => '03-102',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 45,
                'name' => 'Direction Provinciale SEFROU',
                'code' => '03-103',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 46,
                'name' => 'Direction Provinciale TAOUNATE',
                'code' => '03-104',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 47,
                'name' => 'Direction Provinciale BOULEMANE',
                'code' => '03-105',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 48,
                'name' => 'Service administration & finance - Fes-Meknes',
                'code' => '03-B10',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 49,
                'name' => 'Service du génie forestier - Fes-Meknes',
                'code' => '03-B20',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 50,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Fes-Meknes',
                'code' => '03-B30',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 51,
                'name' => 'Service de l\'Animation et du Partenariat - Fes-Meknes',
                'code' => '03-B40',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 52,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Fes-Meknes',
                'code' => '03-B50',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 35,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 53,
                'name' => 'DIRECTIONS REGIONALES ANEF - Rabat Salé Kénitra',
                'code' => '04-000',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 54,
                'name' => 'Centre Technique d\'Amélioration des Peuplements Forestiers',
                'code' => '04-001',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 55,
                'name' => 'Direction Provinciale RABAT',
                'code' => '04-100',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 56,
                'name' => 'Direction Provinciale KÉNITRA',
                'code' => '04-101',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 57,
                'name' => 'Direction Provinciale KHEMISSET',
                'code' => '04-102',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 58,
                'name' => 'Direction Provinciale SIDI SLIMANE',
                'code' => '04-103',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 59,
                'name' => 'Direction Provinciale SIDI KACEM',
                'code' => '04-600',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 60,
                'name' => 'Service administration & finance - Rabat-Sale-Kenitra',
                'code' => '04-B10',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 61,
                'name' => 'Service du génie forestier - Rabat-Sale-Kenitra',
                'code' => '04-B20',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 62,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Rabat-Sale-Kenitra',
                'code' => '04-B30',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 63,
                'name' => 'Service de l\'Animation et du Partenariat - Rabat-Sale-Kenitra',
                'code' => '04-B40',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 64,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Rabat-Sale-Kenitra',
                'code' => '04-B50',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 53,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 65,
                'name' => 'DIRECTIONS REGIONALES ANEF - Béni Mellal - Khénifra',
                'code' => '05-000',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 66,
                'name' => 'Parc National KHENIFRA',
                'code' => '05-001',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 67,
                'name' => 'Direction Provinciale BENI MELLAL',
                'code' => '05-100',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 68,
                'name' => 'Direction Provinciale AZILAL',
                'code' => '05-101',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 69,
                'name' => 'Direction Provinciale KHENIFRA',
                'code' => '05-102',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 70,
                'name' => 'Direction Provinciale KHOURIBGA',
                'code' => '05-103',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 71,
                'name' => 'Service administration & finance - Beni Mellal-Khenifra',
                'code' => '05-B10',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 72,
                'name' => 'Service du génie forestier - Beni Mellal-Khenifra',
                'code' => '05-B20',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 73,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Beni Mellal-Khenifra',
                'code' => '05-B30',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 74,
                'name' => 'Service de l\'Animation et du Partenariat - Beni Mellal-Khenifra',
                'code' => '05-B40',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 75,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Beni Mellal-Khenifra',
                'code' => '05-B50',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 65,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 76,
                'name' => 'DIRECTIONS REGIONALES ANEF - Grand Casablanca Settat',
                'code' => '06-000',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 77,
                'name' => 'Direction Provinciale CASABLANCA',
                'code' => '06-100',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 78,
                'name' => 'Direction Provinciale SETTAT',
                'code' => '06-101',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 79,
                'name' => 'Direction Provinciale BENSLIMANE',
                'code' => '06-102',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 80,
                'name' => 'Direction Provinciale EL JADIDA',
                'code' => '06-103',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 81,
                'name' => 'Service administration & finance - Casablanca-Settat',
                'code' => '06-B10',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 82,
                'name' => 'Service du génie forestier - Casablanca-Settat',
                'code' => '06-B20',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 83,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Casablanca-Settat',
                'code' => '06-B30',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 84,
                'name' => 'Service de l\'Animation et du Partenariat - Casablanca-Settat',
                'code' => '06-B40',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 76,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 85,
                'name' => 'DIRECTIONS REGIONALES ANEF - Marrakech Safi',
                'code' => '07-000',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 86,
                'name' => 'Parc National TOUBKAL',
                'code' => '07-001',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 87,
                'name' => 'Direction Provinciale MARRAKECH',
                'code' => '07-100',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 88,
                'name' => 'Direction Provinciale ESSAOUIRA',
                'code' => '07-101',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 89,
                'name' => 'Direction Provinciale SAFI',
                'code' => '07-102',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 90,
                'name' => 'Direction Provinciale RHAMNA',
                'code' => '07-103',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 91,
                'name' => 'Direction Provinciale CHICHAOUA',
                'code' => '07-104',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 92,
                'name' => 'Direction Provinciale EL-KELAA DES SRAGHNA',
                'code' => '07-105',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 93,
                'name' => 'Direction Provinciale EL HAOUZ',
                'code' => '07-200',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 94,
                'name' => 'Direction Provinciale YOUSSOUFIA',
                'code' => '07-500',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 95,
                'name' => 'Service administration & finance - Marrakech-Safi',
                'code' => '07-B10',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 96,
                'name' => 'Service du génie forestier - Marrakech-Safi',
                'code' => '07-B20',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 97,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Marrakech-Safi',
                'code' => '07-B30',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 98,
                'name' => 'Service de l\'Animation et du Partenariat - Marrakech-Safi',
                'code' => '07-B40',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 99,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Marrakech-Safi',
                'code' => '07-B50',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 85,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 100,
                'name' => 'DIRECTIONS REGIONALES ANEF - Draa Tafilalet',
                'code' => '08-000',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 101,
                'name' => 'Parc National Haut Atlas Oriental',
                'code' => '08-002',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 102,
                'name' => 'Direction Provinciale ERRACHIDIA',
                'code' => '08-100',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 103,
                'name' => 'Direction Provinciale MIDELT',
                'code' => '08-101',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 104,
                'name' => 'Direction Provinciale OUARZAZATE',
                'code' => '08-102',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 105,
                'name' => 'Direction Provinciale TINGHIR',
                'code' => '08-103',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 106,
                'name' => 'Direction Provinciale ZAGORA',
                'code' => '08-104',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 107,
                'name' => 'Service administration & finance - Draa-Tafilalet',
                'code' => '08-B10',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 108,
                'name' => 'Service du génie forestier - Draa-Tafilalet',
                'code' => '08-B20',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 109,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Draa-Tafilalet',
                'code' => '08-B30',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 110,
                'name' => 'Service de l\'Animation et du Partenariat - Draa-Tafilalet',
                'code' => '08-B40',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 100,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 111,
                'name' => 'DIRECTIONS REGIONALES ANEF - Souss Massa',
                'code' => '09-000',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 112,
                'name' => 'Centre Technique de Suivi de la Désertification - Souss-Massa',
                'code' => '09-001',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 113,
                'name' => 'Parc National IRIQUI',
                'code' => '09-002',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 114,
                'name' => 'Parc National SOUSS MASSA',
                'code' => '09-003',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 115,
                'name' => 'Direction Provinciale AGADIR',
                'code' => '09-100',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 116,
                'name' => 'Direction Provinciale TAROUDANT',
                'code' => '09-101',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 117,
                'name' => 'Direction Provinciale TIZNIT',
                'code' => '09-102',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 118,
                'name' => 'Direction Provinciale CHTOUKA',
                'code' => '09-103',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 119,
                'name' => 'Direction Provinciale TATA',
                'code' => '09-104',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 120,
                'name' => 'Service administration & finance - Souss-Massa',
                'code' => '09-B10',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 121,
                'name' => 'Service du génie forestier - Souss-Massa',
                'code' => '09-B20',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 122,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Souss-Massa',
                'code' => '09-B30',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 123,
                'name' => 'Service de l\'Animation et du Partenariat - Souss-Massa',
                'code' => '09-B40',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 124,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Souss-Massa',
                'code' => '09-B50',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 111,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 125,
                'name' => 'Unité de la Faune Sauvage',
                'code' => '0x-0x0',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 126,
                'name' => 'DIRECTIONS REGIONALES ANEF - Guelmim Oued Noun',
                'code' => '10-000',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 127,
                'name' => 'Direction Provinciale GUELMIM',
                'code' => '10-100',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 128,
                'name' => 'Direction Provinciale ASSA-ZAG',
                'code' => '10-101',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 129,
                'name' => 'Direction Provinciale TANTAN',
                'code' => '10-102',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 130,
                'name' => 'Direction Provinciale SIDI IFNI',
                'code' => '10-103',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 131,
                'name' => 'Direction Provinciale ASSA-ZAG',
                'code' => '10-400',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 132,
                'name' => 'Service administration & finance - Guelmim-Oued Noun',
                'code' => '10-B10',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 133,
                'name' => 'Service du génie forestier - Guelmim-Oued Noun',
                'code' => '10-B20',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 134,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Guelmim-Oued Noun',
                'code' => '10-B30',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 126,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 135,
                'name' => 'DIRECTIONS REGIONALES ANEF - Laayoune Sakia El Hamra',
                'code' => '11-000',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 136,
                'name' => 'Parc National KHENIFISS',
                'code' => '11-001',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 137,
                'name' => 'Direction Provinciale LAAYOUNE',
                'code' => '11-100',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 138,
                'name' => 'Direction Provinciale BOUJDOUR',
                'code' => '11-101',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 139,
                'name' => 'Direction Provinciale ES-SMARA',
                'code' => '11-102',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 140,
                'name' => 'Direction Provinciale TARFAYA',
                'code' => '11-200',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 141,
                'name' => 'Service administration & finance - Laayoune-Sakia El Hamra',
                'code' => '11-B10',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 142,
                'name' => 'Service du génie forestier - Laayoune-Sakia El Hamra',
                'code' => '11-B20',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 143,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Laayoune-Sakia El Hamra',
                'code' => '11-B30',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 144,
                'name' => 'Service de l\'Animation et du Partenariat - Laayoune-Sakia El Hamra',
                'code' => '11-B40',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 145,
                'name' => 'Service Régional des Etudes et d\'Aménagement - Laayoune-Sakia El Hamra',
                'code' => '11-B50',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 135,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 146,
                'name' => 'DIRECTIONS REGIONALES ANEF - Dakhla Oued Eddahab',
                'code' => '12-000',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 147,
                'name' => 'Direction Provinciale OUED EDDAHAB',
                'code' => '12-100',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 146,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 148,
                'name' => 'Direction Provinciale AOUSSERD',
                'code' => '12-200',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 146,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 149,
                'name' => 'Service administration & finance - Dakhla-Oued Eddahab',
                'code' => '12-B10',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 146,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 150,
                'name' => 'Service du génie forestier - Dakhla-Oued Eddahab',
                'code' => '12-B20',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 146,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 151,
                'name' => 'Service du domaine forestier et de la police des eaux et forêts - Dakhla-Oued Eddahab',
                'code' => '12-B30',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 146,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 152,
                'name' => 'Directeur Général',
                'code' => 'A1-000',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => null,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 153,
                'name' => 'Département de la Communication et de la Coopération',
                'code' => 'A1-010',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 154,
                'name' => 'Service de la Coopération',
                'code' => 'A1-011',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 155,
                'name' => 'Service de Suivi des Conventions Internationales',
                'code' => 'A1-012',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 156,
                'name' => 'Service de la Communication',
                'code' => 'A1-013',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 157,
                'name' => 'Direction de l\'Audit Interne et des Risques',
                'code' => 'A1-100',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 158,
                'name' => 'Département Audit et Évaluation',
                'code' => 'A1-110',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 157,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 159,
                'name' => 'Service de l\'Audit',
                'code' => 'A1-111',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 160,
                'name' => 'Service d\'Evaluation',
                'code' => 'A1-112',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 162,
                'name' => 'Service Gestion des Risques',
                'code' => 'A1-121',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 163,
                'name' => 'Service Méthodes et Organisation',
                'code' => 'A1-122',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 164,
                'name' => 'Département Coordination et Requêtes',
                'code' => 'A1-130',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 157,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 165,
                'name' => 'Service de Coordination avec les Structures Déconcentrées',
                'code' => 'A1-131',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 166,
                'name' => 'Service des Requêtes et des Relations avec le Médiateur',
                'code' => 'A1-132',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 167,
                'name' => 'Secrétaire Général',
                'code' => 'A2-000',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 152,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 168,
                'name' => 'Département des Affaires Juridiques',
                'code' => 'A2-010',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 169,
                'name' => 'Service du Contentieux',
                'code' => 'A2-011',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 170,
                'name' => 'Service des Transactions Foncières',
                'code' => 'A2-012',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 171,
                'name' => 'Service de la Législation et Réglementation',
                'code' => 'A2-013',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 172,
                'name' => 'Centre Innovation, Recherche et Formation',
                'code' => 'A2-020',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 243,
                'name' => 'Service de l\'Amélioration Génétique des Arbres Forestiers et de Sylviculture',
                'code' => 'A2-020-001',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 172,
                'type' => null,
                'entity_type' => 'service',
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 244,
                'name' => 'Service de Technologie du Bois et des Produits Forestiers',
                'code' => 'A2-020-002',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 172,
                'type' => null,
                'entity_type' => 'service',
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 245,
                'name' => 'Service d\'Écologie, de Biodiversité et d\'Érosion des Sols',
                'code' => 'A2-020-003',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 172,
                'type' => null,
                'entity_type' => 'service',
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 246,
                'name' => 'Service de l\'Ingénierie des Compétences et de la Gestion de la Formation',
                'code' => 'A2-020-004',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 172,
                'type' => null,
                'entity_type' => 'service',
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 173,
                'name' => 'Service d\'Amélioration Génétique des Arbres Forestiers et de la Sylviculture',
                'code' => 'A2-021',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 174,
                'name' => 'Service Technologie de Bois et de Produits Forestiers',
                'code' => 'A2-022',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 175,
                'name' => 'Service d\'Ecologie, Biodiversité et Erosion des Sols',
                'code' => 'A2-023',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 176,
                'name' => 'Service de l\'Ingénierie des Compétences et de la Gestion de la Formation',
                'code' => 'A2-024',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 177,
                'name' => 'Direction du Capital Humain et de la Logistique',
                'code' => 'A3-100',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 178,
                'name' => 'Département des Ressources Humaines',
                'code' => 'A3-110',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 177,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 179,
                'name' => 'Service des Affaires du Personnel',
                'code' => 'A3-111',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 178,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 180,
                'name' => 'Service de la Gestion Prévisionnelle des Effectifs et des Compétences',
                'code' => 'A3-112',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 178,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 181,
                'name' => 'Service des Affaires Sociales',
                'code' => 'A3-113',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 178,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 182,
                'name' => 'Département des Achats',
                'code' => 'A3-120',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 177,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 183,
                'name' => 'Service Programmation et Normalisation',
                'code' => 'A3-121',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 182,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 184,
                'name' => 'Service Support de l\'Acte d\'Achat et des Appels d\'Offres',
                'code' => 'A3-122',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 182,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 185,
                'name' => 'Service de Suivi de l\'Exécution des Marchés',
                'code' => 'A3-123',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 182,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 186,
                'name' => 'Département de la Logistique',
                'code' => 'A3-130',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 177,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 187,
                'name' => 'Service des Moyens Généraux',
                'code' => 'A3-131',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 186,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 188,
                'name' => 'Service de l\'Inventaire',
                'code' => 'A3-132',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 186,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 189,
                'name' => 'Service de Parc Auto',
                'code' => 'A3-133',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 186,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 190,
                'name' => 'Direction Financière et des Solutions Digitales',
                'code' => 'A3-200',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 191,
                'name' => 'Service du Contrôle de Gestion',
                'code' => 'A3-201',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 190,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 192,
                'name' => 'Département de la Gestion Financière et de la Programmation',
                'code' => 'A3-210',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 190,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 193,
                'name' => 'Service Financier',
                'code' => 'A3-211',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 192,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 194,
                'name' => 'Service de la Programmation Budgétaire',
                'code' => 'A3-212',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 192,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 195,
                'name' => 'Service de la Comptabilité',
                'code' => 'A3-213',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 192,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 196,
                'name' => 'Département des Systèmes d\'Information et des Solutions Digitales',
                'code' => 'A3-220',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 190,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 197,
                'name' => 'Service du Système d\'Information et de la Cybersécurité',
                'code' => 'A3-221',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 196,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 198,
                'name' => 'Service de Développement des Solutions Digitales',
                'code' => 'A3-222',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 196,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 199,
                'name' => 'Service des Statistiques Forestières et de la Documentation',
                'code' => 'A3-223',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 196,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 200,
                'name' => 'Direction de l\'Economie Forestière, de l\'Animation Territoriale et du Partenariat',
                'code' => 'A3-300',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 201,
                'name' => 'Département de l\'Economie Forestière',
                'code' => 'A3-310',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 200,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 202,
                'name' => 'Service de l\'Organisation de l\'Exploitation Forestière',
                'code' => 'A3-311',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 201,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 203,
                'name' => 'Service de la Valorisation des Produits Forestiers',
                'code' => 'A3-312',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 201,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 204,
                'name' => 'Service des Études et Inventaire Forestier National',
                'code' => 'A3-313',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 201,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 205,
                'name' => 'Département de l\'Animation Territoriale et du Partenariat',
                'code' => 'A3-320',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 200,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 206,
                'name' => 'Service de l\'Animation Territoriale et Partenariat',
                'code' => 'A3-321',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 205,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 207,
                'name' => 'Service des Parcours Forestiers et Sylvopastoraux',
                'code' => 'A3-322',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 205,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 208,
                'name' => 'Service des Forêts Urbaine et Périurbaines et d\'Accueil du Public',
                'code' => 'A3-323',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 205,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 209,
                'name' => 'Direction des Parcs Nationaux, des Aires Protégées et de la Protection de la Nature',
                'code' => 'A3-400',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 210,
                'name' => 'Département des Parcs Nationaux et des Aires Protégées',
                'code' => 'A3-410',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 209,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 211,
                'name' => 'Service d\'Aménagement des Parcs Nationaux et des Aires Protégées',
                'code' => 'A3-411',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 210,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 212,
                'name' => 'Service de l\'Ecologie et de la Conservation de la Flore et de la Faune Sauvage',
                'code' => 'A3-412',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 210,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 213,
                'name' => 'Service de l\'Ecotourisme',
                'code' => 'A3-413',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 210,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 214,
                'name' => 'Département de la Chasse et de la Cynégétique',
                'code' => 'A3-420',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 209,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 215,
                'name' => 'Service de la Chasse Associative',
                'code' => 'A3-421',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 214,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 216,
                'name' => 'Service de la Chasse Touristique',
                'code' => 'A3-422',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 214,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 217,
                'name' => 'Département de la Pêche et de l\'Aquaculture Continentale',
                'code' => 'A3-430',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 209,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 218,
                'name' => 'Service de la Pêche Continentale',
                'code' => 'A3-431',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 217,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 219,
                'name' => 'Service de l\'Aquaculture Continentale',
                'code' => 'A3-432',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 217,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 220,
                'name' => 'Service d\'Hydrobiologie et de Pisciculture',
                'code' => 'A3-433',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 217,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 221,
                'name' => 'Direction du Patrimoine Forestier et de la Police des Eaux et Forêts',
                'code' => 'A3-500',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 222,
                'name' => 'Département du Domaine Forestier',
                'code' => 'A3-510',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 221,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 223,
                'name' => 'Service de la Délimitation du Domaine Forestier',
                'code' => 'A3-511',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 222,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 224,
                'name' => 'Service de la Mobilisation du Domaine Forestier',
                'code' => 'A3-512',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 222,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 225,
                'name' => 'Service des Infrastructures et Équipements Forestiers',
                'code' => 'A3-513',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 222,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 226,
                'name' => 'Département de la Police des Eaux et Forêts',
                'code' => 'A3-520',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 221,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 227,
                'name' => 'Service de Déontologie et Veille Informationnelle',
                'code' => 'A3-521',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 226,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 228,
                'name' => 'Service de Suivi des Délits et de Verbalisations',
                'code' => 'A3-522',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 226,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 229,
                'name' => 'Service des Transactions Avant Jugement',
                'code' => 'A3-523',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 226,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 230,
                'name' => 'Direction de la Reforestation, des Risques Climatiques et Environnementaux',
                'code' => 'A3-600',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 167,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 231,
                'name' => 'Département de l\'Ingénierie de l\'Aménagement',
                'code' => 'A3-610',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 230,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 232,
                'name' => 'Service de l\'Aménagement des Forêts',
                'code' => 'A3-611',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 231,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 233,
                'name' => 'Service de la Conservation des Sols et de l\'Aménagement des Bassins Versants',
                'code' => 'A3-612',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 231,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 234,
                'name' => 'Service de la mise en Œuvre des plans d\'Aménagement',
                'code' => 'A3-613',
                'date_debut' => '2023-11-20',
                'date_fin' => null,
                'parent_id' => 231,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 239,
                'name' => 'Département des Risques Climatiques et Environnementaux',
                'code' => 'A3-630',
                'date_debut' => '2022-11-20',
                'date_fin' => null,
                'parent_id' => 230,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 240,
                'name' => 'Service de l\'Erosion des Sols',
                'code' => 'A3-631',
                'date_debut' => '2021-11-20',
                'date_fin' => null,
                'parent_id' => 239,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 241,
                'name' => 'Service des Incendies des Forêts',
                'code' => 'A3-632',
                'date_debut' => '2020-11-20',
                'date_fin' => null,
                'parent_id' => 239,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
            [
                'id' => 242,
                'name' => 'Service de Santé des Forêts',
                'code' => 'A3-633',
                'date_debut' => '2024-11-20',
                'date_fin' => null,
                'parent_id' => 239,
                'type' => null,
                'entity_type' => null,
                'lieu_affectation' => null,
                'lieu_direction' => null,
            ],
        ];

        // Create entities with their IDs
        foreach ($entitiesData as $entityData) {
            $id = $entityData['id'];
            unset($entityData['id']);
            
            Entite::updateOrCreate(
                ['id' => $id],
                $entityData
            );
        }

        // Create entite_infos
        $entiteInfosData = [
            4 => [
                'description' => 'Direction Régionale de Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            5 => [
                'description' => 'Parc National AL HOCEIMA de la région Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            6 => [
                'description' => 'Parc National TALASSEMTANE de la région Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            7 => [
                'description' => 'Direction Provinciale de TANGER',
                'type' => 'regional',
            ],
            8 => [
                'description' => 'Direction Provinciale de TETOUAN',
                'type' => 'regional',
            ],
            9 => [
                'description' => 'Direction Provinciale de FAHS-ANJRA',
                'type' => 'regional',
            ],
            10 => [
                'description' => 'Direction Provinciale de MDIQ-FNIDEQ',
                'type' => 'regional',
            ],
            11 => [
                'description' => 'Direction Provinciale de LARACHE',
                'type' => 'regional',
            ],
            12 => [
                'description' => 'Direction Provinciale de AL HOCEIMA',
                'type' => 'regional',
            ],
            13 => [
                'description' => 'Direction Provinciale de CHEFCHAOUEN',
                'type' => 'regional',
            ],
            14 => [
                'description' => 'Direction Provinciale de OUAZZANE',
                'type' => 'regional',
            ],
            15 => [
                'description' => 'Service administration & finance de la région Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            16 => [
                'description' => 'Service du génie forestier de la région Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            17 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forets de la région Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            18 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            19 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Tanger Tétouan Al Hoceima',
                'type' => 'regional',
            ],
            20 => [
                'description' => 'Direction Régionale de l\'Oriental',
                'type' => 'regional',
            ],
            21 => [
                'description' => 'Centre Technique de Suivi de la Désertification de la région Oriental',
                'type' => 'regional',
            ],
            22 => [
                'description' => 'Direction Provinciale de OUJDA',
                'type' => 'regional',
            ],
            23 => [
                'description' => 'Direction Provinciale de NADOR',
                'type' => 'regional',
            ],
            24 => [
                'description' => 'Direction Provinciale de DRIOUCHE',
                'type' => 'regional',
            ],
            25 => [
                'description' => 'Direction Provinciale de BERKANE',
                'type' => 'regional',
            ],
            26 => [
                'description' => 'Direction Provinciale de JERADA',
                'type' => 'regional',
            ],
            27 => [
                'description' => 'Direction Provinciale de GUERCHIF',
                'type' => 'regional',
            ],
            28 => [
                'description' => 'Direction Provinciale de FIGUIG',
                'type' => 'regional',
            ],
            29 => [
                'description' => 'Direction Provinciale de TAOURIRT',
                'type' => 'regional',
            ],
            30 => [
                'description' => 'Service administration & finance de la région Oriental',
                'type' => 'regional',
            ],
            31 => [
                'description' => 'Service du génie forestier de la région Oriental',
                'type' => 'regional',
            ],
            32 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forets de la région Oriental',
                'type' => 'regional',
            ],
            33 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Oriental',
                'type' => 'regional',
            ],
            34 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Oriental',
                'type' => 'regional',
            ],
            35 => [
                'description' => 'Direction Régionale de Fès-Meknès',
                'type' => 'regional',
            ],
            36 => [
                'description' => '- Participer à l\'élaboration d\'une plate-forme et une interface d\'échange, de concertation, de réflexion générale et de forces de propositions visant la gestion et la protection de la faune sauvage 
- Apporter l\'appui technique aux structures de gestion concernées, aux associations et au secteur privé, notamment en matière d\'élevage du gibier, d\'organisation des territoires de chasse et de choix des techniques pour leur exploitation et leur aménagement cynégétique 
- Participer à la promotion de la communication, à la diffusion de l\'information et au développement les partenariats entre tous les acteurs concernés par la faune sauvage 
- Abriter les manifestations et les expositions relatives à la faune sauvage, aux activités cynégétiques liées à la chasse 
- Contribuer à l\'enrichissement des travaux du conseil supérieur de la chasse et à la mise en œuvre de ses recommandations 
- Proposer des méthodes et des approches de gestion expérimentées et validées 
- Etablir des constats, aider à discerner des tendances, évaluer les statuts des espèces et proposer des mesures de réponses appropriées.',
                'type' => 'regional',
            ],
            37 => [
                'description' => 'Parc National IFRANE de la région Fes-Meknes',
                'type' => 'regional',
            ],
            38 => [
                'description' => 'Parc National TAZZEKKA de la région Fes-Meknes',
                'type' => 'regional',
            ],
            39 => [
                'description' => 'Centre Technique de Développement des Ressources Cynégétiques de la région Fes-Meknes',
                'type' => 'regional',
            ],
            40 => [
                'description' => '- Contribuer à la gestion des ressources halieutiques et veiller sur l\'état des eaux continentales 
- Elaborer les programmes de production et d\'empoissonnement et veiller sur leur mise en œuvre 
- Apporter l\'assistance technique et l\'encadrement requis aux investisseurs privés dans le domaine de la pisciculture 
- Contribuer aux opérations de lutte contre l\'eutrophisation 
- Multiplier et réhabiliter les espèces de poissons autochtones 
- Contrôler l\'introduction de nouvelles espèces à valeur économique 
- Réaliser et initier les études concernant les espèces aquacoles et les études hydrobiologiques des écosystèmes aquatiques 
- Superviser les opérations de production de poissons et d\'empoissonnement des oueds, lacs et retenues de barrage ',
                'type' => 'regional',
            ],
            41 => [
                'description' => 'Centre Nationale d\'Hydrobiologie et de Pisciculture de la région Fes-Meknes',
                'type' => 'regional',
            ],
            42 => [
                'description' => 'Direction Provinciale de MEKNES-ELHAJEB',
                'type' => 'regional',
            ],
            43 => [
                'description' => 'Direction Provinciale de TAZA',
                'type' => 'regional',
            ],
            44 => [
                'description' => 'Direction Provinciale de IFRANE',
                'type' => 'regional',
            ],
            45 => [
                'description' => 'Direction Provinciale de SEFROU',
                'type' => 'regional',
            ],
            46 => [
                'description' => 'Direction Provinciale de TAOUNATE',
                'type' => 'regional',
            ],
            47 => [
                'description' => 'Direction Provinciale de BOULEMANE',
                'type' => 'regional',
            ],
            48 => [
                'description' => 'Service administration & finance de la région Fes-Meknes',
                'type' => 'regional',
            ],
            49 => [
                'description' => 'Service du génie forestier de la région Fes-Meknes',
                'type' => 'regional',
            ],
            50 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Fes-Meknes',
                'type' => 'regional',
            ],
            51 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Fes-Meknes',
                'type' => 'regional',
            ],
            52 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Fes-Meknes',
                'type' => 'regional',
            ],
            53 => [
                'description' => 'Direction Régionale de Rabat Salé Kénitra',
                'type' => 'regional',
            ],
            54 => [
                'description' => '- Définir, choisir, tester, et diffuser les techniques d\'amélioration des peuplements forestiers 
- Apporter appui aux structures de terrain et analyser l\'efficacité des choix techniques à adopter et les actions à entreprendre pour valoriser les peuplements forestiers 
- Multiplier le matériel végétal de productivité élevée nécessaire pour les repeuplements en espèces naturelles et introduites, et ce en procédant à :
• L\'identification, l\'inventaire, et la collecte des informations relatives à l\'ensemble des essences forestières et vergers à graines à portée nationale 
• La définition, la description et le suivi des traitements sylvicoles des formations forestières et des interventions pour leur protection sanitaire 
• L\'entreprise des prospections au niveau des peuplements forestiers existants, et l\'identification et testage des plants forestiers de productivité élevée et leur distribution aux utilisateurs.',
                'type' => 'regional',
            ],
            55 => [
                'description' => 'Direction Provinciale de RABAT',
                'type' => 'regional',
            ],
            56 => [
                'description' => 'Direction Provinciale de KÉNITRA',
                'type' => 'regional',
            ],
            57 => [
                'description' => 'Direction Provinciale de KHEMISSET',
                'type' => 'regional',
            ],
            58 => [
                'description' => 'Direction Provinciale de SIDI SLIMANE',
                'type' => 'regional',
            ],
            59 => [
                'description' => 'Direction Provinciale de SIDI KACEM',
                'type' => 'regional',
            ],
            60 => [
                'description' => 'Service administration & finance de la région Rabat-Sale-Kenitra',
                'type' => 'regional',
            ],
            61 => [
                'description' => 'Service du génie forestier de la région Rabat-Sale-Kenitra',
                'type' => 'regional',
            ],
            62 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Rabat-Sale-Kenitra',
                'type' => 'regional',
            ],
            63 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Rabat-Sale-Kenitra',
                'type' => 'regional',
            ],
            64 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Rabat-Sale-Kenitra',
                'type' => 'regional',
            ],
            65 => [
                'description' => 'Direction Régionale de Béni Mellal - Khénifra',
                'type' => 'regional',
            ],
            66 => [
                'description' => 'Parc National KHENIFRA de la région Beni Mellal-Khenifra',
                'type' => 'regional',
            ],
            67 => [
                'description' => 'Direction Provinciale de BENI MELLAL',
                'type' => 'regional',
            ],
            68 => [
                'description' => 'Direction Provinciale de AZILAL',
                'type' => 'regional',
            ],
            69 => [
                'description' => 'Direction Provinciale de KHENIFRA',
                'type' => 'regional',
            ],
            70 => [
                'description' => 'Direction Provinciale de KHOURIBGA',
                'type' => 'regional',
            ],
            71 => [
                'description' => 'Service administration & finance de la région Beni Mellal-Khenifra',
                'type' => 'regional',
            ],
            72 => [
                'description' => 'Service du génie forestier de la région Beni Mellal-Khenifra',
                'type' => 'regional',
            ],
            73 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Beni Mellal-Khenifra',
                'type' => 'regional',
            ],
            74 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Beni Mellal-Khenifra',
                'type' => 'regional',
            ],
            75 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Beni Mellal-Khenifra',
                'type' => 'regional',
            ],
            76 => [
                'description' => 'Direction Régionale de Grand Casablanca Settat',
                'type' => 'regional',
            ],
            77 => [
                'description' => 'Direction Provinciale de CASABLANCA',
                'type' => 'regional',
            ],
            78 => [
                'description' => 'Direction Provinciale de SETTAT',
                'type' => 'regional',
            ],
            79 => [
                'description' => 'Direction Provinciale de BENSLIMANE',
                'type' => 'regional',
            ],
            80 => [
                'description' => 'Direction Provinciale de EL JADIDA',
                'type' => 'regional',
            ],
            81 => [
                'description' => 'Service administration & finance de la région Casablanca-Settat',
                'type' => 'regional',
            ],
            82 => [
                'description' => 'Service du génie forestier de la région Casablanca-Settat',
                'type' => 'regional',
            ],
            83 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Casablanca-Settat',
                'type' => 'regional',
            ],
            84 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Casablanca-Settat',
                'type' => 'regional',
            ],
            85 => [
                'description' => 'Direction Régionale de Marrakech Safi',
                'type' => 'regional',
            ],
            86 => [
                'description' => 'Parc National TOUBKAL de la région Marrakech-Safi',
                'type' => 'regional',
            ],
            87 => [
                'description' => 'Direction Provinciale de MARRAKECH',
                'type' => 'regional',
            ],
            88 => [
                'description' => 'Direction Provinciale de ESSAOUIRA',
                'type' => 'regional',
            ],
            89 => [
                'description' => 'Direction Provinciale de SAFI',
                'type' => 'regional',
            ],
            90 => [
                'description' => 'Direction Provinciale de RHAMNA',
                'type' => 'regional',
            ],
            91 => [
                'description' => 'Direction Provinciale de CHICHAOUA',
                'type' => 'regional',
            ],
            92 => [
                'description' => 'Direction Provinciale de EL-KELAA DES SRAGHNA',
                'type' => 'regional',
            ],
            93 => [
                'description' => 'Direction Provinciale de EL HAOUZ',
                'type' => 'regional',
            ],
            94 => [
                'description' => 'Direction Provinciale de YOUSSOUFIA',
                'type' => 'regional',
            ],
            95 => [
                'description' => 'Service administration & finance de la région Marrakech-Safi',
                'type' => 'regional',
            ],
            96 => [
                'description' => 'Service du génie forestier de la région Marrakech-Safi',
                'type' => 'regional',
            ],
            97 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Marrakech-Safi',
                'type' => 'regional',
            ],
            98 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Marrakech-Safi',
                'type' => 'regional',
            ],
            99 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Marrakech-Safi',
                'type' => 'regional',
            ],
            100 => [
                'description' => 'Direction Régionale de Draa Tafilalet',
                'type' => 'regional',
            ],
            101 => [
                'description' => 'Parc National Haut Atlas Oriental de la région Draa-Tafilalet',
                'type' => 'regional',
            ],
            102 => [
                'description' => 'Direction Provinciale de ERRACHIDIA',
                'type' => 'regional',
            ],
            103 => [
                'description' => 'Direction Provinciale de MIDELT',
                'type' => 'regional',
            ],
            104 => [
                'description' => 'Direction Provinciale de OUARZAZATE',
                'type' => 'regional',
            ],
            105 => [
                'description' => 'Direction Provinciale de TINGHIR',
                'type' => 'regional',
            ],
            106 => [
                'description' => 'Direction Provinciale de ZAGORA',
                'type' => 'regional',
            ],
            107 => [
                'description' => 'Service administration & finance de la région Draa-Tafilalet',
                'type' => 'regional',
            ],
            108 => [
                'description' => 'Service du génie forestier de la région Draa-Tafilalet',
                'type' => 'regional',
            ],
            109 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Draa-Tafilalet',
                'type' => 'regional',
            ],
            110 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Draa-Tafilalet',
                'type' => 'regional',
            ],
            111 => [
                'description' => 'Direction Régionale de Souss Massa',
                'type' => 'regional',
            ],
            112 => [
                'description' => 'Centre Technique de Suivi de la Désertification de la région Souss-Massa',
                'type' => 'regional',
            ],
            113 => [
                'description' => 'Parc National IRIQUI de la région Souss-Massa',
                'type' => 'regional',
            ],
            114 => [
                'description' => 'Parc National SOUSS MASSA de la région Souss-Massa',
                'type' => 'regional',
            ],
            115 => [
                'description' => 'Direction Provinciale de AGADIR',
                'type' => 'regional',
            ],
            116 => [
                'description' => 'Direction Provinciale de TAROUDANT',
                'type' => 'regional',
            ],
            117 => [
                'description' => 'Direction Provinciale de TIZNIT',
                'type' => 'regional',
            ],
            118 => [
                'description' => 'Direction Provinciale de CHTOUKA',
                'type' => 'regional',
            ],
            119 => [
                'description' => 'Direction Provinciale de TATA',
                'type' => 'regional',
            ],
            120 => [
                'description' => 'Service administration & finance de la région Souss-Massa',
                'type' => 'regional',
            ],
            121 => [
                'description' => 'Service du génie forestier de la région Souss-Massa',
                'type' => 'regional',
            ],
            122 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Souss-Massa',
                'type' => 'regional',
            ],
            123 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Souss-Massa',
                'type' => 'regional',
            ],
            124 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Souss-Massa',
                'type' => 'regional',
            ],
            125 => [
                'description' => '- Assurer la surveillance et la protection de la faune sauvage et veiller à l\'application de la réglementation 
- Assurer le respect de la réglementation en vigueur et dresser les procès-verbaux de délits se rapportant à la faune sauvage 
- Détecter, alerter et établir les rapports des risques et dysfonctionnements constatés (épidémie, maladie, etc) 
- Participer à l\'élaboration de la cartographie des territoires de chasse et de pêche 
- Traiter, classer et mettre à jour les données : registre d\'ordre, cahier de consignation, cahier journalier 
- Participer au contrôle, à l\'encadrement et à l\'organisation des chasseurs et pêcheurs 
- Assurer l\'encadrement des opérations de capture et de diversement de la faune sauvage 
- Assurer l\'encadrement de ses collaborateurs, en coordination avec la hiérarchie et les autres unités de gestion disposant de la responsabilité territoriale.',
                'type' => 'central',
            ],
            126 => [
                'description' => 'Direction Régionale de Guelmim Oued Noun',
                'type' => 'regional',
            ],
            127 => [
                'description' => 'Direction Provinciale de GUELMIM',
                'type' => 'regional',
            ],
            128 => [
                'description' => 'Direction Provinciale de ASSA-ZAG',
                'type' => 'regional',
            ],
            129 => [
                'description' => 'Direction Provinciale de TANTAN',
                'type' => 'regional',
            ],
            130 => [
                'description' => 'Direction Provinciale de SIDI IFNI',
                'type' => 'regional',
            ],
            131 => [
                'description' => 'Direction Provinciale de ASSA-ZAG',
                'type' => 'regional',
            ],
            132 => [
                'description' => 'Service administration & finance de la région Guelmim-Oued Noun',
                'type' => 'regional',
            ],
            133 => [
                'description' => 'Service du génie forestier de la région Guelmim-Oued Noun',
                'type' => 'regional',
            ],
            134 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Guelmim-Oued Noun',
                'type' => 'regional',
            ],
            135 => [
                'description' => 'Direction Régionale de Laayoune Sakia El Hamra',
                'type' => 'regional',
            ],
            136 => [
                'description' => 'Parc National KHENIFISS de la région Laayoune-Sakia El Hamra',
                'type' => 'regional',
            ],
            137 => [
                'description' => 'Direction Provinciale de LAAYOUNE',
                'type' => 'regional',
            ],
            138 => [
                'description' => 'Direction Provinciale de BOUJDOUR',
                'type' => 'regional',
            ],
            139 => [
                'description' => 'Direction Provinciale de ES-SMARA',
                'type' => 'regional',
            ],
            140 => [
                'description' => 'Direction Provinciale de TARFAYA',
                'type' => 'regional',
            ],
            141 => [
                'description' => 'Service administration & finance de la région Laayoune-Sakia El Hamra',
                'type' => 'regional',
            ],
            142 => [
                'description' => 'Service du génie forestier de la région Laayoune-Sakia El Hamra',
                'type' => 'regional',
            ],
            143 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Laayoune-Sakia El Hamra',
                'type' => 'regional',
            ],
            144 => [
                'description' => 'Service de l\'Animation et du Partenariat de la région Laayoune-Sakia El Hamra',
                'type' => 'regional',
            ],
            145 => [
                'description' => 'Service Régional des Etudes et d\'Aménagement de la région Laayoune-Sakia El Hamra',
                'type' => 'regional',
            ],
            146 => [
                'description' => 'Direction Régionale de Dakhla Oued Eddahab',
                'type' => 'regional',
            ],
            147 => [
                'description' => 'Direction Provinciale de OUED EDDAHAB',
                'type' => 'regional',
            ],
            148 => [
                'description' => 'Direction Provinciale de AOUSSERD',
                'type' => 'regional',
            ],
            149 => [
                'description' => 'Service administration & finance de la région Dakhla-Oued Eddahab',
                'type' => 'regional',
            ],
            150 => [
                'description' => 'Service du génie forestier de la région Dakhla-Oued Eddahab',
                'type' => 'regional',
            ],
            151 => [
                'description' => 'Service du domaine forestier et de la police des eaux et forêts de la région Dakhla-Oued Eddahab',
                'type' => 'regional',
            ],
            152 => [
                'description' => 'Directeur Général de l\'ANEF',
                'type' => 'central',
            ],
            153 => [
                'description' => '- Gérer et coordonner les activités de coopération internationale avec les participants (bi et multi latéraux) et nationale (ambassades, représentations, etc) 
- Participer à la préparation, l\'élaboration et la mise en œuvre des accords de coopération technique (Accord cadre, Protocole, Conventions, etc) 
- Mettre en place et coordonner les programmes de coopération internationale, en concertation avec les entités techniques 
- Gérer, coordonner et superviser le partenariat national
- Prospecter, développer et promouvoir les partenariats et les opportunités de coopération nationale et internationale
- Proposer et mettre en place la stratégie globale de communication interne et externe de l\'Agence
- Proposer et élaborer les plans de communication interne et externe et en superviser la mise en œuvre 
- Développer l\'image de l\'Agence auprès des usagers, des professionnels et des partenaires nationaux et internationaux.',
                'type' => 'central',
            ],
            154 => [
                'description' => '- Élaborer et mettre en œuvre la stratégie de coopération internationale (multilatérale, bilatérale et triangulaire) et nationale de l\'Agence, et d\'en assurer le suivi 
- Établir et développer les relations de coopération avec les acteurs nationaux et internationaux susceptibles d\'accompagner l\'Agence dans la réalisation de ses objectifs stratégiques 
- Promouvoir la coopération bilatérale et coordonner le processus de préparation des accords y afférents depuis la négociation jusqu\'à la signature 
- Promouvoir la coopération nationale et coordonner le processus de préparation des conventions y afférentes depuis la négociation jusqu\'à la signature 
- Coordonner la préparation des programmes et projets de coopération depuis l\'identification jusqu\'à la signature des accords et conventions de financement 
- Assurer le suivi de la mise en œuvre des programmes et projets de coopération en étroite collaboration avec les Directions concernées de l\'Agence 
- Coordonner la gestion des missions à l\'étranger et l\'accueil des délégations étrangères au Maroc.',
                'type' => 'central',
            ],
            155 => [
                'description' => '- Coordonner la mise en œuvre, à l\'échelle nationale, des dispositions des conventions internationales dont l\'Agence est le point focal national 
- Assurer, conformément aux prérogatives de l\'Agence, la réalisation des engagements du Maroc dans le cadre des conventions internationales en collaboration avec les Départements/Etablissements qui en assurent la coordination à l\'échelle nationale 
- Mettre en place, au niveau national, un dispositif de suivi-évaluation des initiatives et programmes lancés dans le cadre des conventions internationales 
- Veiller à la participation active de l\'Agence aux différentes réunions statutaires et aux évènements thématiques organisés dans le cadre des conventions internationales.',
                'type' => 'central',
            ],
            156 => [
                'description' => '- Élaborer et mettre en œuvre la stratégie de communication de l\'Agence et veiller au suivi de son exécution 
- Concevoir et exécuter les plans de communication Interne et Externe, et en assurer le suivi 
- Promouvoir et renforcer la culture de l\'Agence (interne) et entretenir sa notoriété et son image institutionnelle (externe) 
- Organiser des campagnes de communication et de sensibilisation visant la préservation et la protection des écosystèmes forestiers 
- Assurer la gestion des relations publiques et des relations presse 
- Contribuer à la production des différents documents, outils et supports de communication 
- Coordonner l\'organisation des éventements thématiques pilotés par l\'Agence (conférences, séminaires, salons, foires, célébration des journées nationales et internationales, etc) 
- Coordonner la participation de l\'Agence aux éventements traitant des thèmes en relation avec ses domaines de compétence 
- Administrer et mettre à jour le portail Internet de l\'Agence, et promouvoir l\'utilisation des nouvelles technologies de l\'information et de la communication.',
                'type' => 'central',
            ],
            157 => [
                'description' => '- Mettre en place le dispositif de pilotage et de mesure de la performance de l\'Agence 
- Mesurer, contrôler et prévoir les résultats opérationnels de l\'Agence 
- Assurer le reporting des résultats au comité de direction 
- Effectuer les audits internes en examiner les conséquences, émettre ses critiques et proposer des améliorations à la Direction Générale 
- Diagnostiquer les dysfonctonnements dans les procédures de gestion, d\'organisation ou dans les méthodes de travail 
- Apporter des recommandations à la Direction Générale en matière d\'améliorations ou de réajustements à effectuer au regard d\'impératifs réglementaires ou de performance 
- Réaliser des études et analyses sur les activités et l\'efficience de l\'Agence 
- Concevoir, développer, mettre à jour et améliorer le système qualité de l\'Agence, au niveau central et au niveau déconcentré 
- Assurer la coordination et le suivi des actions d\'assurance qualité aux niveaux des directions, départements et services.',
                'type' => 'central',
            ],
            158 => [
                'description' => '- Proposer le dispositif de l\'audit et piloter sa mise en œuvre 
- Définir les objectifs d\'audit 
- Superviser la réalisation des missions d\'audit 
- Evaluer le dispositif d\'audit 
- Coordonner et rendre compte de l\'avancement des missions d\'audit 
- Assurer le suivi des recommandations des rapports d\'audit 
- Assurer le reporting des différents chantiers de I\'ANEF',
                'type' => 'central',
            ],
            159 => [
                'description' => '- Elaborer le plan d\'audit interne, définir ses objectifs et établir le planning de sa réalisation 
- Suivre la réalisation des missions d\'audit interne 
- Coordonner et rendre compte de l\'avancement des missions d\'audit interne 
- Assurer le suivi des recommandations des rapports d\'audit 
- Elaborer le rapport annuel des activités de l\'audit interne 
- Préparer les procédures de l\'audit interne 
- Elaborer la charte de l\'audit interne en coordination avec les autres structures 
- Participer à la réalisation des différentes missions de la Direction.',
                'type' => 'central',
            ],
            160 => [
                'description' => '- Participer à la mise en place du dispositif de pilotage et d\'évaluation de la performance de l\'Agence 
- Participer au suivi et analyse de la performance 
- Assurer le suivi de l\'amélioration continue de la performance de l\'Agence 
- Elaborer les rapports d\'évaluation des activités et de la performance de l\'Agence.
- Participer à la réalisation des différentes missions de la Direction.',
                'type' => 'central',
            ],
            162 => [
                'description' => '- Elaborer el dispositif du management des risques et coordonner sa mise en œuvre 
- Guider l\'élaboration et la mise à jour de la cartographie des risques des différentes activités et structures de l\'Agence 
- Aider les différentes structures à adopter la gestion des risques comme outil de gestion 
- Elaborer le rapport annuel des activités de gestion des risques 
- Participer à la réalisation des différentes missions de la Direction.',
                'type' => 'central',
            ],
            163 => [
                'description' => '- Analyser les méthodes, les procédures et l\'organisation de travail 
- Participer à l\'élaboration et le suivi des plans d\'actions de gestion des risques 
- Assurer l\'amélioration continue des méthodes, des procédures et d\'organisation de travail 
- Elaborer une base de données des procédures et méthodes de travail et veiller à sa mise à jour 
- Assurer la promotion de la démarche qualité de l\'Agence 
- Elaborer le rapport annuel d\'activité du service 
- Participer à la réalisation des différentes missions de la Direction.',
                'type' => 'central',
            ],
            164 => [
                'description' => '- Assurer la coordination avec les structures déconcentrées de l\'Agence en agissant en tant qu\'interlocuteur de la Direction de l\'audit interne et des risques 
- Assurer le suivi des plaintes, doléances et demandes de règlement des différends transmises par l\'Institution du Médiateur et veiller à leur traitement, dans les délais impartis, et au suivi des recommandations formulées à leur issue 
- Inciter les structures déconcentrées à faire preuve de responsabilité, d\'efficacité et de transparence dans leurs rapports avec l\'Institution du Médiateur, ses délégués spéciaux et les médiateurs régionaux 
- Proposer les mesures et autres dispositions à même d\'améliorer les conditions d\'accueil des usagers et des contribuables et de simplifier les procédures administratives 
- Tenir et conserver une base de données des plaintes et doléances et des mesures y afférentes 
- Elaborer le rapport annuel permettant le suivi de l\'action de l\'Agence en matière de plaintes, de doléances et de demandes de règlement des différends dont elle est saisie.',
                'type' => 'central',
            ],
            165 => [
                'description' => '- Veiller à la promotion des activités de contrôle au niveau des structures déconcentrées 
- Inciter les structures déconcentrées à faire preuve de responsabilité, d\'efficacité et de transparence dans leurs rapports avec l\'Institution du Médiateur, ses délégués spéciaux et les médiateurs régionaux 
- Elaborer le rapport annuel des activités du service 
- Participer à la réalisation des différentes missions de la Direction.',
                'type' => 'central',
            ],
            166 => [
                'description' => '- Instruire et coordonner le traitement des requêtes parvenues à l\'Agence 
- Assurer le suivi des plaintes et doléances transmises par l\'Institution du Médiateur, veiller à leur traitement, dans les délais impartis, et au suivi des recommandations y afférentes 
- Assurer le suivi des réclamations, des observations et des propositions des citoyens via le Portail National des réclamations 
- Proposer les mesures et autres dispositions pour améliorer les conditions d\'accueil des usagers et des contribuables et simplifier les procédures administratives 
- Elaborer le rapport annuel permettant le suivi du traitement des requêtes parvenues à l\'Agence 
- Participer à la réalisation des différentes missions de la Direction.',
                'type' => 'central',
            ],
            167 => [
                'description' => 'Secrétaire Général de l\'ANEF',
                'type' => 'central',
            ],
            168 => [
                'description' => '- Réaliser les études juridiques et préparer les projets de lois et règlements relatifs à l\'administration du domaine forestier de l\'Etat et en assurer le suivi 
- Veiller sur la conformité juridique des actes 
- Assurer l\'étude des risques juridiques inhérents aux actes 
- Elaborer les actes juridiques de l\'Agence 
- Assurer l\'étude précontentieux et le suivi du contentieux 
- Assurer le suivi des transactions avant jugement 
- Assurer le suivi des dossiers transactions foncières et des contentieux en représentant l\'Agence 
- Assurer la veille des publications de textes et projets de textes juridiques et élaborer les avis relatifs aux projets de textes juridiques notamment en ce qui concerne les lois et règlements se rapportant aux domaines de l\'Agence.',
                'type' => 'central',
            ],
            169 => [
                'description' => '- Superviser l\'instruction et assurer le suivi des dossiers relatifs au contentieux 
- Assurer le suivi de la plateforme informatique dédiée au contentieux 
- Assurer la coordination avec les points focaux régionaux chargés du suivi du contentieux 
- Définir un dispositif de prévention, en agissant en amont sur les causes pouvant conduire au contentieux 
- Représenter l\'ANEF dans les instances nationales concernées par le contentieux.',
                'type' => 'central',
            ],
            170 => [
                'description' => '- Assurer le traitement et le suivi des dossiers des transactions foncières touchant le domaine forestier et représenter l\'ANEF dans les instances nationales concernées par ces opérations 
- Elaborer les actes juridiques relatifs aux transactions foncières 
- Assurer le suivi de la plateforme informatique dédiée aux transactions foncières.',
                'type' => 'central',
            ],
            171 => [
                'description' => '- Réaliser les études juridiques et préparer les projets de lois et règlements relatifs à l\'administration du domaine forestier de l\'Etat et en assurer le suivi 
- Coordonner l\'examen des projets de lois et règlements émanant des différents départements ministériels ou autres organismes, et en relation avec les domaines de compétence de l\'ANEF.',
                'type' => 'central',
            ],
            172 => [
                'description' => '- Elaborer et déployer un programme de recherche pragmatique et contribuant à court, moyen et long terme au renforcement de la performance de l\'Agence dans ses différents domaines d\'activités 
- Assoir le Pôle Recherche et Formation dans le cadre d\'un rapprochement synergétique entre l\'Agence et les opérateurs de l\'enseignement technique en relation avec les domaines de l\'Agence 
- Animer le schéma directeur de la formation Continue au profit du personnel de l\'Agence en collaboration avec la Direction du Capital Humain et de la Logistique.',
                'type' => 'central',
            ],
            173 => [
                'description' => '- Développer des projets de recherches appliqués dans les domaines d\'amélioration génétique, de sylviculture et de santé de forêts à moyen terme 
- Concourir à la planification opérationnelle et fonctionnelle des projets promus 
- Promouvoir les formes de partenariat institutionnel autour des problématiques techniques liées aux programmes de reboisement, de production de plants, de sylviculture, de connaissance des pathologies et des ravageurs 
- Développer des projets de conservation de ressources génétiques forestières 
- Assurer la gestion des laboratoires, pépinières, parcelles expérimentales, parcelles d\'essais génétiques ainsi que les arboretums 
- Assurer l\'encadrement des unités de recherche thématiques',
                'type' => 'central',
            ],
            174 => [
                'description' => '- Parachever les projets de caractérisation physico-mécaniques des bois d\'espèces forestières et promouvoir des procédés innovants de transformation et de développement des chaînes de valeurs 
- Planifier et encadrer la réalisation des projets de recherche-développement dans les domaines de biomasse énergie, efficacité énergétiques, valorisation des plantes aromatiques et médicinales, de qualification de liège et PFNL (résines, Biochar, HE, anti-oxydants…) 
- Promouvoir des formes de partenariats institutionnels et privés autour des projets de recherches incubés dans le service 
- Assurer la gestion des laboratoires et de l\'unité industrielle du bois 
- Assurer l\'encadrement des unités de recherche et concourir à la vulgarisation des résultats de recherche',
                'type' => 'central',
            ],
            175 => [
                'description' => '- Planifier à moyen terme des projets de recherches relatifs aux études écologiques, biogéographiques et des populations d\'espèces cynégétiques et sauvages et en assurer la réalisation et le suivi 
- Capitaliser sur les acquis de recherche en érosion hydrique des sols, des dynamiques d\'érosion éoliennes et des techniques de lutte 
- Promouvoir des solutions techniques innovantes en termes de restauration des écosystèmes forestiers et de lutte contre l\'érosion (hydrique et éolienne) 
- Assurer l\'encadrement des unités de recherches et concourir à la vulgarisation des résultats de recherche.',
                'type' => 'central',
            ],
            176 => [
                'description' => '- Identifier les déficits de compétence du capital humain de l\'ANEF 
- Elaborer et mettre en œuvre le programme de formation au niveau central, appuyer et encadrer les services régionaux concernés dans l\'élaboration, la mise en œuvre et l\'évaluation des plans de formation regionaux 
- Gérer les marchés relatifs à la formation au niveau central et déléguer les crédits pour la réalisation des plans de formation regionaux 
- Evaluer le plan de formation et élaborer le bilan annuel des réalisations en matière de formation par l\'ANEF à l\'échelle nationale (central et régional) 
- Gérer les dossiers d\'envoi en stage de formation (de longue durée) et de perfectionnement (courte durée) au Maroc et à l\'étranger 
- Gérer les demandes de stage des étudiants des établissements de formation',
                'type' => 'central',
            ],
            177 => [
                'description' => '- Le déploiement de la stratégie de l\'agence en élaborant une stratégie RH appropriée dans le cadre intégré d\'une gestion prévisionnelle des emplois et des compétences, cohérente avec les besoins de l\'Agence et sa stratégie 
- La bonne application des dispositions du Statut particulier du personnel 
- La gestion des relations avec les institutions en charge des affaires sociales 
- La mise en œuvre d\'une politique des achats cohérente avec la stratégie de l\'agence et conforme à la réglementation en vigueur 
- L\'établissement en concertation avec les entités prescriptrices du planning annuel des achats et son exécution 
- La satisfaction des besoins achats exprimés par l\'ensemble des entités de l\'Agence dans des conditions optimales en matière d\'initiation et d\'exécution de l\'acte d\'achat 
- La gestion de la logistique, de l\'inventaire, du parc auto, des assurances et des affaires générales.',
                'type' => 'central',
            ],
            178 => [
                'description' => '- Le déploiement de la stratégie de l\'agence en élaborant une stratégie RH appropriée dans le cadre Intégré d\'une gestion prévisionnelle des emplois et des compétences, cohérente avec les besoins de l\'Agence et sa stratégie 
- La bonne application des dispositions du Statut du personnel 
- La gestion des relations et des affaires sociales.',
                'type' => 'central',
            ],
            179 => [
                'description' => '- Gérer la vie administrative du personnel et mettre à jour les dossiers et actes administratifs, veiller sur leur archivage et optimiser leur gestion électronique 
- Assurer la gestion des postes budgétaires 
- Mobiliser les actes et les processus nécessaires à la régularisation de la situation administrative et financière du personnel 
- Assurer le suivi des dossiers disciplinaires et de contentieux administratif relatifs à la gestion du personnel 
- Assurer la veille règlementaire en matière de gestion administrative du personnel 
- Piloter et mettre en production le SIRH 
- Assurer la gestion des requêtes en relation avec le personnel.',
                'type' => 'central',
            ],
            180 => [
                'description' => '- Définir les besoins de l\'ANEF en effectifs et compétences en prévision de l\'évolution de ses missions et activités 
- Concevoir et mettre en œuvre les parcours professionnels.
- Préparer et mettre en œuvre une politique de gestion et de valorisation des ressources humaines 
- Diffuser les bonnes pratiques en matière de gestion des ressources humaines 
- Elaborer et Piloter le système d\'évaluation du personnel et en assurer le suivi 
- Définir les besoins en recrutement, organiser les concours et assurer l\'insertion des nouvelles recrues 
- Gérer les nominations et les appels à candidatures 
- Organiser les examens d\'aptitude professionnelle 
- Superviser l\'opération de mobilité.',
                'type' => 'central',
            ],
            181 => [
                'description' => '- Maintenir et renforcer le dialogue social 
- Organiser les élections des représentants du personnel 
- Gérer les relations avec les représentations des différentes catégories du personnel 
- Animer la vie professionnelle du personnel de l\'ANEF 
- Piloter la communication interne de la fonction Ressources Humaines 
- Contribuer à l\'élaboration du plan d\'action annuel des œuvres sociales 
- Préparer les réunions avec les partenaires sociaux et suivre la mise en œuvre des résultats et conclusions 
- Traiter les affaires relatives à la santé et à la sécurité au travail 
- Gérer des avantages en nature accordés au personnel 
- Contribuer à la mise en œuvre de l\'approche genre et à la lutte contre les formes d\'exclusions 
- Mettre en place des mécanismes de coordination permanents avec les organismes de retraite et de prévoyance sociale et optimiser la position du personnel de l\'ANEF 
- Gérer les dossiers de retraite et régularisation des situations relatives aux sorties de service.',
                'type' => 'central',
            ],
            182 => [
                'description' => '- Définir et mettre en œuvre une politique des achats cohérente avec la stratégie de l\'agence et conforme à la réglementation en vigueur 
- Etablir annuellement, en concertation avec les entités prescriptrices, le planning annuel des achats et veiller à son exécution 
- Définir les règles méthodologiques applicables en matière d\'achats 
- Veiller à la satisfaction des besoins achats exprimés par l\'ensemble des entités de l\'Agence dans des conditions optimales en matière d\'initiation et d\'exécution de l\'acte d\'achat 
- S\'assurer de la conformité des achats réalisés avec la réglementation, les règles et les procédures en vigueur 
- Œuvrer à la promotion d\'une forte culture d\'éthique, de rigueur et de transparence au sein du processus Achats.',
                'type' => 'central',
            ],
            183 => [
                'description' => '- Elaborer, en concertation avec les entités prescriptrices de l\'Agence, le planning annuel des achats et veiller à son respect et à son exécution 
- Etablir un référentiel d\'achat et un manuel des procédures fondés sur l\'éthique, l\'impartialité et l\'objectivité 
- S\'assurer de la conformité des achats réalisés avec la réglementation, les règles et les procédures en vigueur 
- Veiller sur l\'élaboration et le respect d\'une déontologie des achats promouvant la responsabilité environnementale de l\'Agence, agissant contre toute forme de corruption et tenant compte du respect des principes fondamentaux tels que le travail des enfants 
- Garantir l\'exemplarité des achats de l\'Agence tant en termes des processus d\'achats qu\'en termes de qualité des produits fournis et des procédés de livraison et d\'exécution des prestations.',
                'type' => 'central',
            ],
            184 => [
                'description' => '- Veiller sur l\'élaboration des CPS types et arrêter les prescriptions communes à chaque type de prestation 
- Elaborer les dossiers d\'appel d\'offres et des autres actes d\'achats 
- Veiller au lancement des appels d\'offres et à la mise en œuvre des différentes modalités de passation de la commande publique en conformité avec la réglementation en vigueur et dans le respect du planning et du contenu du programme annuel des achats 
- Optimiser le processus de la dépense et de consultations des prestataires 
- Organiser et participer aux commissions d\'ouverture des plis ',
                'type' => 'central',
            ],
            185 => [
                'description' => '- Mettre en œuvre le dispositif de contrôle et de suivi de réalisation 
- Superviser le contrôle et l\'approbation des dossiers des marchés 
- Gérer les relations avec les différents partenaires institutionnels, relevant du champ d\'action du service 
- Assurer le suivi règlementaire de l\'exécution et de la liquidation de la dépense (Notification, ordre de service, mise en demeure, résiliation, etc) 
- Vérifier les pièces justifiant l\'exigibilité de la dépense (attachements, acomptes, décomptes, factures, ordres d\'arrêt et de reprise, délai d\'exécution,etc) 
- Optimiser le contrôle quantitatif et qualitatif du service fait.',
                'type' => 'central',
            ],
            186 => [
                'description' => '- Définir les besoins en dotations matérielles et préparer les éléments pour l\'élaboration des prévisions budgétaires et du programme d\'emploi 
- Contribuer au suivi des opérations d\'approvisionnement et d\'achat des biens et des services en veillant sur la qualité du service fait 
- Superviser la gestion des ressources matérielles et veiller à la rationalisation de leur utilisation 
- Digitaliser la gestion de la logistique et assurer son extension aux structures déconcentrées de l\'ANEF 
- Assurer l\'organisation des opérations d\'inventaire et de réforme du matériel et du patrimoine 
- Mobiliser les tableaux de bord de suivi des activités des services 
- Procéder à l\'évaluation périodique du patrimoine de l\'Agence et prendre les mesures requises pour sa sauvegarde et la maintenance de ses équipements.',
                'type' => 'central',
            ],
            187 => [
                'description' => '- Consolider les besoins en moyens par toutes les structures de l\'Agence 
- Elaborer les programmes prévisionnels d\'achats (PPA) 
- Élaborer une base de données fournisseurs pour le lancement des commandes 
- Etablir des référentiels des prix pour l\'estimation des coûts des prestations 
- Mettre en place des tableaux de bord de suivi de l\'utilisation des moyens 
- Moderniser la gestion et le conditionnement des magasins pour maitriser les flux de stocks 
- Réaliser des audits d\'une façon régulière de la consommation des dotations 
- Moderniser et digitaliser le système de gestion des activités du Service 
- Gérer les bâtiments et les dépenses eau et électricité 
- Intégrer les standards de la Stratégie Nationale du Développement Durable.',
                'type' => 'central',
            ],
            188 => [
                'description' => '- Moderniser et digitaliser le système de gestion des activités du Service 
- Instaurer les mesures à même de répondre aux exigences de la comptabilité matière 
- Etablir une nouvelle instruction de l\'inventaire compatible avec les classes de la comptabilité générale 
- Instaurer de nouveaux process pour l\'organisation des opérations de l\'inventaire, de la réforme, de destruction ou de mise en vente des différents biens de l\'Agence.
- Prendre les mesures requises pour la maintenance des équipements de l\'Agence par la mise en place d\'une solution de gestion de la maintenance assistée.',
                'type' => 'central',
            ],
            189 => [
                'description' => '- Disposer d\'une connaissance poussée sur le parc automobile de l\'ANEF et sur son fonctionnement 
- Moderniser et digitaliser le système de gestion des activités du service 
- Veiller à l\'entretien et au renouvellement du parc automobile 
- Se doter d\'une check-list des garages agrées et validés avec des normes de consultation 
- Instaurer un système de géolocalisation pour le parc automobile 
- Etablir les taxes et les assurances de toute la flotte du parc automobile 
- Identifier les postes de dépenses à revoir, à optimiser et à suivre afin de rationaliser la gestion de la flotte automobile.',
                'type' => 'central',
            ],
            190 => [
                'description' => '- Mettre en œuvre et veiller à l\'amélioration continue de la démarche de la planification intégrée des recettes et dépenses qui fait le lien entre les planifications stratégiques et opérationnelles 
- Veiller à la présentation transparente de l\'information comptable et financière et coordonner les travaux du Commissaire aux comptes 
- Veiller à la préparation du budget en concertation avec l\'ensemble des structures centrales et déconcentrées, au suivi de son exécution, et au contrôle des dépenses et au pilotage des recettes 
- Concevoir, développer, mettre à jour et améliorer le système qualité de l\'Agence, au niveau central et au niveau déconcentré 
- Assurer la conception et veiller à la mise en place et au maintien du système de management intégré des recettes et dépenses 
- Porter assistance au métier dans la conception et le développement d\'outils de gestion intégrés articulés autour des systèmes d\'information alimentés au niveau du terrain 
- Gérer le volet applicatif du Système d\'Information 
- Veiller à la mise en œuvre des projets informatiques et de la maintenance applicative conformément aux normes et standards définis en la matière.',
                'type' => 'central',
            ],
            191 => [
                'description' => '- Mesurer, contrôler et prévoir les résultats opérationnels de l\'Agence 
- Effectuer des audits sur les procédures comptables et financières 
- Effectuer les audits internes, en examiner les conséquences, émettre ses critiques et proposer des améliorations à la Direction 
- Diagnostiquer les dysfonctionnements dans les procédures de gestion, d\'organisation ou dans les méthodes de travail 
- Apporter des recommandations à la Direction en matière d\'améliorations ou de réajustements à effectuer au regard des impératifs réglementaires ou de performance 
- Concevoir, développer, mettre à jour et améliorer le système qualité de l\'Agence, au niveau central et au niveau déconcentré 
- Assurer la coordination et le suivi des actions d\'assurance qualité aux niveaux des directions, départements et services.',
                'type' => 'central',
            ],
            192 => [
                'description' => '- Mettre en œuvre et veiller à l\'amélioration continue de la démarche de la planification intégrée qui fait le lien entre les planifications stratégiques et opérationnelles 
- Coordonner les opérations et assurer le suivi de la planification stratégique et de l\'ensemble du processus budgétaire, tout en veillant à une allocation optimale des ressources financières 
- Veiller à la présentation transparente de l\'information comptable et financière et coordonner les travaux du Commissaire aux comptes 
- Veiller à la préparation du budget en concertation avec l\'ensemble des structures centrales et déconcentrées, au suivi de son exécution, et au contrôle des dépenses et au pilotage des recettes.',
                'type' => 'central',
            ],
            193 => [
                'description' => '- Contribuer à la mise en place de la stratégie financière de l\'Agence et veiller à sa mise en œuvre 
- Assurer le pilotage et l\'amélioration des recettes et veiller à l\'équilibre financier entre les recettes et les dépenses de l\'Agence 
- Elaborer les bilans et rapports financiers et communiquer à leur propos avec les partenaires externes de l\'agence 
- Veiller à la présentation transparente de l\'information financière et assurer la coordination avec le Commissaire aux comptes.',
                'type' => 'central',
            ],
            194 => [
                'description' => '- Mettre en œuvre et veiller à l\'amélioration continue de la programmation opérationnelle de l\'agence 
- Coordonner l\'élaboration d\'une programmation triennale optimale et veiller au suivi de l\'ensemble du processus de la programmation budgétaire 
- Coordonner l\'élaboration du budget global de l\'Agence avec l\'ensemble des entités centrales et régionales, tout en veillant à l\'optimisation de l\'allocation des ressources financières et à leur rationalisation 
- Veiller à l\'amélioration des outils de contractualisation et de pilotage de la performance au sein de l\'Agence.',
                'type' => 'central',
            ],
            195 => [
                'description' => '- Mettre en œuvre la nouvelle politique budgétaire et comptable de l\'Agence 
- Assurer la qualité et la présentation transparente de l\'information comptable 
- Veiller au respect des procédures comptables et financières et à la tenue de la comptabilité 
- Assurer le contrôle des dépenses et le suivi régulier de la gestion budgétaire et comptable de l\'ANEF et œuvrer à son amélioration 
- Elaborer les comptes annuels.',
                'type' => 'central',
            ],
            196 => [
                'description' => '- Porter assistance au métier dans la conception et le développement d\'outils de gestion intégrés articulés autour d\'un système d\'information géographique alimenté au niveau du terrain 
- Gérer le volet applicatif du Système d\'Information 
- Veiller à la mise en œuvre des projets informatiques et de la maintenance applicative conformément aux normes et standards définis en la matière 
- Apporter conseil et assistance en organisation et en conduite de projets 
- Définir les normes internes revêtant un caractère transversal en matière d\'organisation, de méthodes et de qualité et veille à leur application 
- Assurer la conception et veiller à la mise en place et au maintien du système de management intégré.',
                'type' => 'central',
            ],
            197 => [
                'description' => '- Elaborer le plan d\'action du service conformément aux orientations du Département des systèmes d\'information et des solutions digitales 
- Administrer, maintenir et faire évoluer les logiciels et les matériels 
- Animer, coordonner et manager une équipe d\'administrateurs systèmes et une équipe de techniciens informatique en charge de l\'assistance des services centraux et régionaux 
- Définir les moyens et les procédures pour garantir les performances et la disponibilité des systèmes informatiques, réseaux et télécommunications 
- Contribuer dans la définition et l\'application des normes et standards de sécurité et la PSSI (politique de sécurité des systèmes d\'information) de l\'ANEF 
- Assurer la veille technologique.',
                'type' => 'central',
            ],
            198 => [
                'description' => '- Elaborer le plan d\'action du service conformément aux orientations du département des systèmes d\'information et des solutions digitales 
- Développer et déployer la stratégie de la transformation numérique et digitale de l\'ANEF 
- Assurer la faisabilité, la réalisation et le suivi technique des projets de développement digital 
- Développer une culture d\'innovation digitale et introduire des outils et nouvelles méthodes 
- Animer, coordonner et manager une équipe d\'administrateurs systèmes et une équipe de techniciens de développement digital 
- Assurer la veille technologique.',
                'type' => 'central',
            ],
            199 => [
                'description' => '- Elaborer le plan d\'action du service conformément aux orientations du département des systèmes d\'information et des solutions digitales 
- Piloter le déploiement des solutions 
- Administrer et piloter la centralisation des données dans un système d\'information géographique unique 
- Collecter et centraliser toutes les informations statistiques forestières 
- Concevoir et mettre au point des modalités pratiques de la collecte et de traitement des données statistiques forestières 
- Développer, maintenir et exploiter la base des données statistiques forestières 
- Mettre en place un fonds documentaire pertinent sur toutes les activités forestières de l\'ANEF 
- Animer, coordonner et manager une équipe des statisticiens.',
                'type' => 'central',
            ],
            200 => [
                'description' => '- Organisation de l\'exploitation forestière 
- Valorisation des Produits Forestiers 
- Études et Inventaire Forestier National 
- Animation territoriale et Partenariat 
- Parcours Forestiers et Sylvopastoraux 
- Les FUP et l\'accueil du public.',
                'type' => 'central',
            ],
            201 => [
                'description' => '- Œuvrer pour valoriser les produits et services procurés par la forêt et améliorer sa contribution dans la satisfaction des besoins du pays 
- Préparer et étudier les projets de lois et règlements et toutes les mesures techniques, économiques et organisationnelles susceptibles de promouvoir le développement des filières et des organisations professionnelles forestières, en suivre l\'exécution et en évaluer les impacts 
- Mener les études et contribuer à la préparation des opérations d\'exploitation  Assurer la gestion des cessions et recettes des produits forestiers 
- Assurer la gestion des dossiers de bois particuliers 
- Assurer la gestion des droits d\'usage des populations riveraines.',
                'type' => 'central',
            ],
            202 => [
                'description' => '- Encadrer la planification et la mise en œuvre des programmes des exploitations forestières 
- Suivre et évaluer la mise en œuvre des programmes de coupe 
- Assurer la vérification et le suivi des recettes forestières 
- Développer les principes, les méthodes et les outils d\'analyse de la rentabilité économique 
- Elaborer les guides et les manuels sur les méthodes d\'analyse économique et financière ainsi que sur les méthodes de mesurage 
- Suivre et mettre à jour les bases de données sur l\'exploitation forestière 
- Contribuer au contrôle des travaux des exploitations forestières 
- Assurer la gestion et le contrôle de l\'exploitation des bois particuliers.',
                'type' => 'central',
            ],
            203 => [
                'description' => '- Suivre et encadrer la planification et la mise en œuvre des programmes de récolte de liège 
- Participer à l\'opération de marquage des lièges 
- Suivre la préparation des dossiers d\'appels d\'offres pour la récolte de liège 
- Planifier et suivre les programmes de concessions forestières,
- Préparer les dossiers d\'appels d\'offres relatifs aux projets de concessions forestières 
- Lancer les appels d\'offres relatifs aux projets de concessions forestières 
- Suivre les programmes de contrats de partenariat avec les coopératives 
- Développer les chaînes de valeurs des filières de valorisation des produits forestiers 
- Développer les interprofessions relatives aux filières de valorisation des produits forestiers.',
                'type' => 'central',
            ],
            204 => [
                'description' => '- Préparer et étudier les projets de lois et règlements et toutes les mesures techniques, économiques et organisationnelles susceptibles de promouvoir le développement des filières et des organisations professionnelles forestières, en suivre l\'exécution et en évaluer les impacts 
- Créer et développer une base de données biophysiques et socioéconomiques dynamiques fondées sur un dispositif permanent de collecte des données géoréférencées 
- Produire une banque des données en termes de résultats sur : les étendues et les potentialités ligneuses des ressources forestières nationales, les facteurs d\'émissions des gaz à effet de serre, la biomasse et le carbone biologique des forêts du Maroc, la biodiversité, les problèmes environnementaux et la gestion forestière 
- Renforcer l\'arsenal réglementaire et technique en matière d\'inventaire de nos forêts dont : les manuels de collecte et de traitement des données, le matériel technique et l\'équipement moderne.',
                'type' => 'central',
            ],
            205 => [
                'description' => '- Coordonner la préparation et la mise en œuvre des programmes et projets de développement intégré des zones forestières et péri-forestières 
- Contribuer à la promotion de l\'écotourisme en forêt et dans les parcs et réserves naturelles 
- Valoriser les approches partenariales et participatives pour le développement durable et la préservation de la biodiversité dans les parcs 
- Diriger, activer et coordonner les approches participatives pour la préparation des plans d\'aménagement et de gestion des parcs 
- Coordonner le travail des différentes unités chargées de l\'animation des parcs, les accompagner, leur fournir les documents et données nécessaires, et veiller à l\'intégration des recommandations qui en emanent 
- Mettre en œuvre les procédures visant à faire connaître les réserves naturelles et soutenir les programmes de leur découverte, d\'information, de sensibilisation et d\'éducation environnementale 
- Développer les différentes formes de communication et partenariats avec les acteurs concernés par la forêt 
- Assurer l\'appui et l\'encadrement des usagers aux actions d\'auto-développement en relation avec la forêt 
- Contribuer au développement socioéconomique des populations riveraines.',
                'type' => 'central',
            ],
            206 => [
                'description' => '- La dynamiser l\'implication des acteurs forestiers notamment les usagers des forêts dans la gestion territoriale des forêts et des aires protégées 
- Contribuer à la dynamisation et à l\'appréciation des fonctions économiques, scientifiques, sociales, environnementales et culturelles des réserves naturelles 
- Valoriser les approches partenariales et participatives pour le développement durable des forêts et la préservation de la biodiversité dans les parcs 
- Coordonner, et accompagner le travail des différentes unités chargées de l\'animation territoriale et du partenariat en relation avec le domaine forestier et les aires protégées 
- Développer les différentes formes d\'organisation des acteurs et des filières relevant du secteur forestier 
- Développer les différentes formes de communication et de partenariats avec les acteurs concernés par la forêt 
- Contribuer à la promotion de l\'écotourisme en forêt et dans les parcs et réserves naturelles 
- Mettre en œuvre les procédures visant à faire connaître les réserves naturelles et soutenir les programmes de sensibilisation et d\'éducation environnementale',
                'type' => 'central',
            ],
            207 => [
                'description' => '- Assurer le suivi de la mise en œuvre de la stratégie sylvopastorale de l\'ANEF au niveau national 
- Suivre et encadrer la planification et la réalisation des programmes d\'aménagement des parcours forestiers et sylvopastoraux au niveau national 
- Suivre et encadrer, au niveau national, les programmes et les dossiers d\'octroi de la compensation de mise en défens au profit des groupements d\'usagers (associations et coopératives) 
- Assurer l\'appui et l\'encadrement des usagers aux actions d\'auto-développement en relation avec la forêt 
- Contribuer au développement socioéconomique des populations riveraines.',
                'type' => 'central',
            ],
            208 => [
                'description' => '- Contribuer à la promotion de l\'écotourisme dans les Forêts Urbaines et Périurbaines et dans les aires protégées 
- Mettre en œuvre les procédures visant la gestion des Forêts Urbaines et Périurbaines et soutenir les programmes de leur aménagement, de sensibilisation et d\'éducation à l\'environnement 
- Contribuer à la dynamisation et à l\'appréciation des fonctions économiques, scientifiques, sociales, environnementales et culturelles des réserves naturelles 
- Contribuer au développement des Forêts Urbaines et Périurbaines au niveau national 
- Développer les partenariats pour la gestion durable des Forêts Urbaines et Périurbaines au niveau national.',
                'type' => 'central',
            ],
            209 => [
                'description' => '- Participer au développement du réseau des parcs nationaux à travers la mise en œuvre du processus de concertation et de classement des aires protégées 
- Proposer la création de parcs nationaux ou leur extension 
- Contribuer à la conservation, la gestion durable, la réhabilitation et la restauration de la flore et de la faune sauvage et de ses habitats naturels 
- Mettre en œuvre les mesures spécifiques de restriction pour certaines activités admises dans les espaces limitrophes des parcs et en assurer le suivi et l\'évaluation.',
                'type' => 'central',
            ],
            210 => [
                'description' => '- Assurer la mise en œuvre de la législation relative aux conditions de création et de gestion des réserves et pacs nationaux 
- Coordonner la préparation des plans d\'aménagement des parcs et réserves naturelles et en assurer le suivi d\'exécution, et l\'évaluation des résultats 
- Assurer le suivi de la mise en œuvre des plans d\'aménagement et de gestion des parcs nationaux et doter les unités territoriales des mécanismes de gestion d\'évaluation nécessaires 
- Œuvrer pour la conservation et la valorisation de la biodiversité 
- Organiser la création des aires protégées 
- Contrôler la gestion des aires protégées (Parcs Nationaux, Réserves Biologiques...)
- Contribuer à la régulation de la population de la faune sauvage pour le maintien des équilibres des écosystèmes.',
                'type' => 'central',
            ],
            211 => [
                'description' => '- Assurer la mise en œuvre de la législation relative aux conditions de création et de gestion des réserves et pacs nationaux 
- Coordonner la préparation des plans d\'aménagement des parcs et réserves naturelles et en assurer le suivi d\'exécution, et l\'évaluation des résultats 
- Assurer le suivi de la mise en œuvre des plans d\'aménagement et de gestion des parcs nationaux et doter les unités territoriales des mécanismes de gestion d\'évaluation nécessaires 
- Organiser la création des aires protégées 
- Contrôler la gestion des aires protégées (Parcs Nationaux, Réserves Biologiques...).',
                'type' => 'central',
            ],
            212 => [
                'description' => '- Contribuer à l\'élaboration de projets de textes législatifs et réglementaires en matière de conservation de la faune et de la flore et coordonner leur application 
- Contribuer à la conservation et la gestion des populations de la faune sauvage pour le maintien des équilibres des écosystèmes 
- Œuvrer pour la conservation et la valorisation de la biodiversité 
- Mettre en place un système et protocole de suivi des espèces de la faune et de la flore sauvage 
- Elaborer et mette en œuvre les plans d\'actions des espèces de faune et de flore 
- Développer des programmes de partenariat en matière de conservation de la flore, de la faune et ses habitats ainsi que leur suivi scientifique 
- Assurer la mise en œuvre au niveau national des conventions CITES, CMS, Ramsar, Berne, AEWA et des recommandations l\'UICN en matière de conservation des espèces menacées d\'extinction 
- Délivrer les permis, licences et autorisations de la faune et de la flore sauvages.',
                'type' => 'central',
            ],
            213 => [
                'description' => '- Identifier et développer les ressources écotouristiques des parcs nationaux et des aires protégées 
- Elaborer et coordonner les plans d\'aménagement écotouristique au niveau national et local 
- Assurer la cohérence des aménagements et des infrastructures écotouristiques 
- Concevoir et développer les produits écotouristiques et la composante accueil au sein des aires protégées 
- Développer les partenariats au niveau national et local avec les opérateurs privés et acteurs du tourisme 
- Assurer le réseautage entre les gestionnaires des aires protégées et les partenaires 
- Veiller sur l\'intégrité de l\'écosystème et assurer le suivi des impacts environnementaux et socioéconomiques du tourisme au niveau des aires protégées ',
                'type' => 'central',
            ],
            214 => [
                'description' => '- Veiller à la préservation et au développement de la faune sauvage et à la dynamisation des activités de chasse 
- Définir le dispositif de collecte et d\'exploitation les données pour suivre la dynamique de la faune sauvage, identifier son état, et proposer des mesures à adopter 
- Apporter l\'expertise technique aux différents intervenants dans le domaine de la chasse dans le secteur privé et public 
- Contribuer à la régulation de la population de la faune sauvage pour le développement des ressources cynégétiques et la protection contre les risques de prolifération de certaines espèces 
- Mener les études et contribuer à la préparation des dossiers d\'amodiation des droits de chasse  Coordonner et organiser l\'activité de chasse au niveau national 
- Assurer le suivi et la gestion des dossiers d\'amodiation de chasse.',
                'type' => 'central',
            ],
            215 => [
                'description' => '- Etudier et suivre les dossiers d\'amodiation du droit de chasse associative 
- Proposer les mesures techniques et réglementaires à entreprendre pour le développement cynégétique au profit des associations de chasse 
- Planifier et suivre la mise en œuvre des aménagements pour la conservation et la valorisation cynégétique de la faune sauvage et de ses habitats 
- Appuyer les gestionnaires dans le domaine de l\'aménagement et de la mise en valeur cynégétique 
- Fournir un cadre de concertation et d\'échange pour le développement cynégétique et développer le partenariat avec les associations de chasse 
- Participer à l\'élaboration des textes législatifs et réglementaires de la chasse associative 
- Contribuer à l\'élaboration des rapports et bilans relatifs aux activités de chasse associative.',
                'type' => 'central',
            ],
            216 => [
                'description' => '- Etudier et suivre les dossiers d\'amodiation du droit de chasse touristique 
- Proposer les mesures techniques et réglementaires à entreprendre pour le développement des produits de chasse touristiques.
- Maitriser le suivi de la dynamique des populations de la faune sauvage notamment des espèces gibiers et des prédateurs 
- Améliorer la connaissance des espèces des habitats et entreprendre les mesures qui s\'imposent pour la protection et la conservation du capital cynégétique et de la biodiversité 
- Fournir un cadre de concertation et d\'échange pour le développement cynégétique et développer le partenariat avec les organisateurs de chasse touristique 
- Participer à l\'élaboration des textes législatifs et réglementaires de la chasse touristique 
- Contribuer à l\'élaboration des rapports et bilans relatifs aux activités de chasse touristiques.',
                'type' => 'central',
            ],
            217 => [
                'description' => '- Veiller à la préservation et au développement des ressources piscicoles et à la dynamisation des activités de pêche 
- Définir le dispositif adéquat de collecte et d\'exploitation des données pour suivre la dynamique ressources piscicoles, identifier son état, et proposer des mesures à adopter 
- Apporter l\'expertise technique aux différents intervenants dans les domaines de la pêche et de la pisciculture dans le secteur privé et public 
- Mener les études et contribuer à la préparation des dossiers d\'amodiation des droits de pêche 
- Coordonner et organiser l\'activité de pêche au niveau national 
- Assurer le suivi et la gestion des dossiers d\'amodiation de pêche 
- Assurer l\'élevage et la production des ressources piscicoles.',
                'type' => 'central',
            ],
            218 => [
                'description' => '- Veiller à la gestion des autorisations du droit de pêche dans les eaux continentales 
- Contribuer au montage et à la mise en œuvre des projets liés au développement de la pêche continentale 
- Assurer l\'appui technique aux associations de pêche de loisir et aux coopératives de pêche commerciale 
- Contribuer à la mise en œuvre des recommandations du comité de pêche et identification des moyens de leur mise en œuvre 
- Contribuer à l\'élaboration et à l\'exécution des schémas régionaux d\'aménagement et de gestion de la pêche et de l\'aquaculture continentales 
- Contribuer à l\'élaboration des rapports et bilans relatifs aux activités du département 
- Participer à l\'élaboration des textes législatifs et réglementaires de la pêche continentale 
- Développer et gérer le partenariat avec des acteurs publics, privés et de la société civile.',
                'type' => 'central',
            ],
            219 => [
                'description' => '- Veiller à la gestion des autorisations d\'implantation des unités aquacoles 
- Apporter l\'assistance technique aux investisseurs privés dans le domaine de l\'aquaculture 
- Contribuer au montage et à la mise en œuvre des projets liés à la promotion de l\'aquaculture continentale 
- Contribuer à la mise en œuvre des recommandations du comité de pêche en matière de développement de l\'aquaculture et identification des moyens de leur mise en œuvre 
- Contribuer à l\'élaboration et à l\'exécution des schémas régionaux d\'aménagement et de gestion de la pêche et de l\'aquaculture continentales 
- Contribuer à l\'élaboration des rapports et bilans relatifs aux activités du département 
- Contribuer à la préparation des textes législatifs et réglementaires régissant l\'activité de l\'aquaculture continentale 
- Développer et gérer le partenariat avec des acteurs publics, privés et de la société civile.',
                'type' => 'central',
            ],
            220 => [
                'description' => '- Assurer le suivi hydrobiologique des milieux aquatiques 
- Veiller à la conservation des espèces de poissons autochtones et de leurs habitats 
- Assurer la production des alevins de poissons et de crustacés au niveau des stations aquacoles relevant de l\'ANEF 
- Assurer l\'aménagement et la mise en valeur piscicole des milieux aquatiques continentaux par les opérations de repeuplement ainsi que de la planification de leur exploitation par la pêche ou par la pisciculture,
- Suivre la dynamique des populations de poissons 
- Gérer les plans d\'eau aux permis spéciaux et leurs valorisations par la pêche de loisir 
- Fournir un cadre de concertation et d\'échange pour le développement halieutique 
- Réaliser des études concernant les espèces et milieux aquacoles.',
                'type' => 'central',
            ],
            221 => [
                'description' => '- Assurer la préparation des programmes de délimitation et d\'immatriculation et en suivre l\'exécution 
- Assurer la maintenance des infrastructures et équipements du domaine forestier 
- Suivre les dossiers des transactions foncières et la mobilisation du domaine forestier 
- Superviser le métier de la police des eaux et forêts en charge de la surveillance du domaine et du contrôle de l\'application des textes législatifs et réglementaires afférents au domaine forestier 
- Définir et mettre en œuvre une stratégie de prévention et répression des infractions commises sur le domaine forestier et assurer le suivi des dossiers.',
                'type' => 'central',
            ],
            222 => [
                'description' => '- Assurer la préparation des programmes de délimitation et d\'immatriculation et d\'en suivre l\'exécution 
- Assurer la maintenance des infrastructures et équipements du domaine forestier 
- Suivre les dossiers des transactions foncières et la mobilisation du domaine forestier 
- Veiller sur la préservation du domaine forestier de l\'Etat et des autres biens soumis au régime forestier 
- Assurer la préparation des programmes de délimitation et d\'immatriculation du domaine forestier et en suivre l\'exécution 
- Assurer la préparation et le suivi des programmes relatifs à la réalisation et à la maintenance des infrastructures et des équipements du domaine forestier de l\'Etat
- Assurer le traitement et le suivi des dossiers des transactions foncières touchant le domaine forestier et représenter !\'Agence dans les instances nationales concernées par ces opérations 
- Superviser l\'instruction et assurer le suivi des dossiers relatifs aux contentieux en matière de domaine forestier 
- Assurer la gestion des infrastructures et installations en domaine forestier.',
                'type' => 'central',
            ],
            223 => [
                'description' => '- Renforcer la base juridique de la domanialité des immeubles forestiers à travers la délimitation et l\'immatriculation foncière 
- Assurer la préparation et le suivi de la mise en œuvre des programmes annuels de délimitation et d\'immatriculation foncière des immeubles forestiers 
- Examiner, vérifier et instruire les dossiers de délimitation et d\'immatriculation des immeubles forestiers conformément à la réglementation en vigueur 
- Contribuer au renforcement des capacités des services déconcentrés dans les domaines de la délimitation forestière, l\'immatriculation foncière et la topographie
- Recueillir, traiter et assurer l\'instruction des requêtes et des contestations des tiers, des collectivités ethniques et des autres Administrations Publiques 
- Participer à l\'élaboration des rapports et bilans pour synthétiser et documenter les activités relevant de sa compétence 
- Veiller sur la qualité des relations avec les partenaires à l\'extérieur.',
                'type' => 'central',
            ],
            224 => [
                'description' => '- Suivre les dossiers des transactions foncières et de mobilisation du domaine forestier 
- Assurer le traitement et le suivi des dossiers des transactions foncières touchant le domaine forestier et représenter l\'Agence dans les instances nationales concernées par ces opérations et par l\'élaboration des stratégies, programmes ou plans de planification territoriale pouvant concerner le domaine forestier 
- Superviser l\'instruction et assurer le suivi des dossiers relatifs aux requêtes et aux contentieux portant sur la mobilisation du domaine forestier 
- Elaborer des situations, rapports et bilans sur ses activités et l\'évolution des dossiers de transactions foncières 
- Veiller sur la qualité des relations avec les partenaires à l\'extérieur.',
                'type' => 'central',
            ],
            225 => [
                'description' => '- Assurer la maintenance des infrastructures et équipements du domaine forestier
- Assurer la préparation et le suivi des programmes relatifs à la réalisation et à la maintenance des infrastructures et des équipements du domaine forestier de l\'Etat 
- Assurer la gestion des infrastructures, équipements et installations en domaine forestier 
- Superviser l\'instruction et le suivi des requêtes et des affaires contentieuses portant sur la réalisation des programmes liés aux infrastructures et équipements du domaine forestier 
- Contribuer au renforcement de capacités des services déconcentrés en matière de préparation, réalisation et suivi des projets d\'infrastructures et équipements forestiers 
- Assurer l\'instruction des demandes présentées par les partenaires publics pour la réalisation des projets de voirie en domaine forestier dans le cadre de conventions de partenariat et suivre leur mise en œuvre 
- Représenter l\'Agence dans les instances nationales agissant dans les domaines des infrastructures et des équipements
- Veiller sur la qualité des relations avec les partenaires à l\'extérieur.',
                'type' => 'central',
            ],
            226 => [
                'description' => '- Mettre en œuvre la stratégie de prévention et de répression des infractions commises sur le domaine forestier 
- Assurer la constatation des infractions et le suivi des délits et des verbalisations 
- Assurer la relation avec les administrations sécuritaires 
- Animer la charte déontologique et la charte vestimentaire du secteur forestier 
- Piloter la veille informationnelle 
- Superviser l\'instruction et assurer le suivi des dossiers relatifs aux contentieux en matière de police forestier 
- Définir les orientations du dispositif de surveillance du patrimoine et de prévention des délits 
- Définir les mécanismes de contrôle des flux des ressources forestières (flore, faune sauvage, produits forestiers...).',
                'type' => 'central',
            ],
            227 => [
                'description' => '- Animer la charte déontologique et la charte vestimentaire du secteur forestier 
- Piloter la veille informationnelle 
- Assurer la relation avec les Administrations sécuritaires et servir de point focal auprès de ces instances pour les besoins de collaboration, d\'appui, de formation et toutes formes d\'échanges 
- Alimenter la grille d\'évaluation permettant l\'appréciation du rendement et de la performance du personnel actif.',
                'type' => 'central',
            ],
            228 => [
                'description' => '- Superviser le métier de la PEF en charge de la surveillance du domaine forestier et du contrôle de l\'application des textes législatifs et réglementaires y afférents 
- Définir les orientations du dispositif de surveillance du patrimoine forestier et de prévention des délits 
- Soutenir les éléments de la PEF incriminés lors de l\'exercice de leur fonction 
- Assurer le suivi des délits et des verbalisations 
- Superviser l\'instruction et assurer le suivi des dossiers relatifs au contentieux pénal 
- Assurer le suivi de la plateforme informatique dédiée au contentieux pénal 
- Elaborer des rapports annuels du contentieux pénal.',
                'type' => 'central',
            ],
            229 => [
                'description' => '- Superviser l\'instruction et assurer le suivi des dossiers relatifs aux transactions avant jugement 
- Assurer le suivi de la plateforme informatique dédiée au contentieux pénal 
- Elaborer les rapports annuels sur les TAJ.',
                'type' => 'central',
            ],
            230 => [
                'description' => '- La planification de la conservation des sols et le suivi des aménagements des ressources forestières, alfatières, sylvopastorales dans les terrains soumis au régime forestier 
- La définition et l\'amélioration continue des itinéraires techniques pour les différentes essences et territoires forestiers, en coordination avec les services déconcentrés de l\'Agence 
- La coordination des actions de développement forestier dans les espaces en interrelation avec les aires protégées et les Parcs Nationaux 
- La protection contre les incendies et la surveillance globale de l\'état de santé des forêts 
- La mise en œuvre de la souveraineté verte (production et certification des semences) ainsi que la gestion des pépinières pour l\'approvisionnement en plants forestiers 
- L\'animation de la cellule de Veille-Crise et La rédaction des Flashs Risque avec une fréquence régulière 
- L\'audit du système de management des risques climatiques et environnementaux 
- La tenue du registre de sécurité selon les normes spécifiques au Management des risques climatiques et environnementaux.',
                'type' => 'central',
            ],
            231 => [
                'description' => '- Coordonner la préparation et superviser la mise en œuvre des plans, programmes et projets de l\'Agence en matière de développement durable des forêts 
- Définir les options de développement et de gestion durable du secteur forestier 
- Définir les orientations relatives à l\'aménagement et aux règlements d\'exploitation des forêts naturelles et artificielles et des nappes alfatières, et en assurer le suivi de l\'exécution 
- Coordonner la préparation des programmes d\'aménagement des bassins versants et de conservation des sols 
- Coordonner la préparation des programmes d\'amélioration sylvopastorale et œuvrer pour la promotion de systèmes d\'organisation compatibles avec une gestion durable des forêts et des parcours forestiers 
- Assurer les fonctions de collecte, de production, de traitement et d\'exploitation des données relatives à l\'inventaire Forestier National 
- Mener les études d\'exploration et de suivi du couvert forestier 
- Superviser la production cartographique forestière et le Système d\'information Géographique de !\'Agence 
- Assurer la fonction de planification pour la gestion des forêts et des parcours forestiers, des aires protégées et le traitement des bassins versants.',
                'type' => 'central',
            ],
            232 => [
                'description' => '- Coordonner et encadrer la planification des programmes nationaux des études d\'aménagement des forêts 
- Contribuer à l\'élaboration des programmes annuels et pluriannuels des études et des plans d\'aménagement des BV 
- Contribuer à la planification locale participative avec les acteurs forestiers 
- Définir les orientations relatives à l\'aménagement et aux règlements d\'exploitation des forêts naturelles et artificielles et des nappes alfatières 
- Contribuer à la production cartographique forestière 
- Contribuer à l\'élaboration des bilans d\'aménagement 
- Contribuer à l\'encadrement de montage de projets de développement socio-économique local.',
                'type' => 'central',
            ],
            233 => [
                'description' => '- Coordonner la préparation des programmes d\'aménagement des bassins versants et de conservation des sols 
- Coordonner et encadrer la planification des programmes nationaux des études d\'aménagement des bassins versants 
- Suivre l\'élaboration et l\'exécution des études d\'aménagement des bassins versants et de conservation des sols en relation avec les différents partenaires concernés 
- Suivre la mise en œuvre des programmes d\'aménagement des bassins versants 
- Suivre la mise en œuvre des conventions spécifiques relatives à l\'aménagement des bassins versants 
- Conduire des campagnes de sensibilisation du grand public sur les enjeux liés à la conservation des sols.',
                'type' => 'central',
            ],
            234 => [
                'description' => '- Suivre et évaluer la mise en œuvre des plans d\'aménagement des forêts dans l\'objectif de développement durable des forêts 
- Elaborer les bilans de réalisation des plans de gestion des forêts 
- Contribuer à la planification nationale des programmes des études d\'aménagement des forêts 
- Participer au suivi et à la validation des études d\'aménagements des forêts 
- Contribuer à la définition des options de développement et de gestion durable du secteur forestier 
- Contribuer au suivi de l\'élaboration des études d\'exploration et de suivi du couvert forestier.',
                'type' => 'central',
            ],
            239 => [
                'description' => 'Assurer La protection contre les incendies et la surveillance globale de l\'état de santé des forêts 
- Animer la cellule de Veille-Crise 
- Assurer la rédaction des Flashs Risque avec une fréquence régulière 
- Assurer Les missions d\'audit du système de management risques climatiques et environnementaux 
- Veiller à la tenue du registre de sécurité selon les normes spécifiques au Management des risques climatiques et Environnementaux 
- Contribuer à la promotion des plans, programmes et projets de lutte contre la désertification et de préservation des ressources naturelles et coordonner la participation de l\'Agence dans leur mise en œuvre 
- Coordonner, au niveau national, la mise en œuvre de la convention des Nations Unies sur la Lutte contre la Désertification 
- Assurer la mise en place d\'un dispositif de suivi des processus de désertification et des programmes et projets pour y remédier 
- Participer à la préparation des programmes d\'aménagement des bassins versants et de conservation des sols, et en assurer le suivi de l\'exécution 
- Définir les stratégies de protection des forêts contre les adversités (incendies, maladies, parasitisme) et en assurer la mise en œuvre et le suivi 
- Coordonner les activités de prévention, de détection et de lutte contre les incendies de forêts 
- Assurer le suivi de la santé et de la vitalité des forêts et définir les mécanismes de veille phytosanitaire 
- Suivre et coordonner les activités de conservation des eaux et sols au niveau national.',
                'type' => 'central',
            ],
            240 => [
                'description' => '- Coordonner, au niveau national, la mise en œuvre de la convention des Nations Unies sur la Lutte contre la désertification 
- Assurer la mise en place d\'un dispositif de suivi des processus de désertification et des programmes et projets pour y remédier 
- Participer à la préparation des programmes d\'aménagement des bassins versants et de conservation des sols et en assurer le suivi de l\'exécution 
- Définir les enjeux, choisir les techniques, conduire et évaluer les travaux de lutte contre la désertification 
- Etudier la dynamique de la désertification 
- Identifier les zones affectées par la désertification ou à risques et établir les priorités 
- Concerter les populations et les instances concernées par le phénomène 
- Choisir les techniques adaptées (en fonction des contraintes techniques et du contexte socio économique de la zone).',
                'type' => 'central',
            ],
            241 => [
                'description' => '- Elaborer les études du milieu et dresser les cartes de sensibilité aux incendies des massifs forestiers 
- Elaborer les plans de prévention des incendies 
- Elaborer les cartes de risques dynamiques 
- Etablir des bilans et des rapports périodiques 
- Organiser la permanence et la vigie pendant les périodes à haut risque 
- Organiser et conduire les actions de sensibilisation aux dangers des incendies à destination du public 
- Organiser les campagnes de prévention et de lutte et mettre en œuvre, en collaboration avec les partenaires, le dispositif y afférent 
- Assurer la coordination des différents intervenants dans la lutte 
- Evaluer le système de prévention et de lutte mis en œuvre et présenter les conclusions et les améliorations qui s\'imposent 
- Assurer la mise en place d\'un système de veille sur les risques d\'incendies forestiers 
- Contribuer à la formation et à la sensibilisation des collaborateurs, partenaires et public 
- Coordonner et suivre la lutte contre les incendies de forêts en relation avec les partenaires concernés 
- Collecter et suivre en temps réel les informations relatives aux incendies de forêts et leur compilation dans la Base de Données réservées à cet effet (alerte des départs de feu, fiches roses, fax, rapports…) 
- Conduire des campagnes de sensibilisation du grand public sur le danger des incendies de forêts.',
                'type' => 'central',
            ],
            242 => [
                'description' => '- Suivre les dysfonctionnements phytosanitaires et les niveaux d\'infestations des différents ravageurs touchant les peuplements forestiers 
- Gérer la base de données de la santé des forêts 
- Suivre l\'exécution des programmes de protection des forêts en relation avec les départements concernés 
- Contribuer à la réalisation des études bioécologiques et climatiques 
- Coordonner les études entomologiques sur les principaux agents pathogènes de la forêt (ravageurs primaires et secondaires) 
- Elaborer les études sur les différentes problématiques de dysfonctionnement phytosanitaire des forêts (dépérissement, singe magot…) 
- Elaborer les études sur la sensibilité des essences forestières 
- Assurer l\'organisation de l\'information et participer à l\'élaboration d\'une base de données SIG des peuplements/ périmètres infectés (acquisition d\'images satellitaires et photographies aériennes, application de la télédétection spatiale) 
- Assurer l\'analyse et la synthèse des études entreprises 
- Coordonner et organiser les tournées de prospection sur le terrain pour approcher les dysfonctionnements phytosanitaires et établir le diagnostic (superficie infectée, intensité d\'attaque…) 
- Contribuer à la définition des traitements à envisager : type d\'intervention (insecticides, traitement sylvicole…) et arrêter le planning d\'exécution des opérations de lutte 
- Coordonner la mise en place d\'un système de suivi scientifique de l\'état sanitaire de la forêt
- Mettre à jour la base de données sur l\'état sanitaire de la forêt 
- Organiser la remontée de l\'information 
- Mettre en place un dispositif de surveillance phytosanitaire.',
                'type' => 'central',
            ],
            243 => [
                'description' => '- Développer des projets de recherches appliqués dans les domaines d\'amélioration génétique, de sylviculture et de santé de forêts à moyen terme 
- Concourir à la planification opérationnelle et fonctionnelle des projets promus 
- Promouvoir les formes de partenariat institutionnel autour des problématiques techniques liées aux programmes de reboisement, de production de plants, de sylviculture, de connaissance des pathologies et des ravageurs 
- Développer des projets de conservation de ressources génétiques forestières 
- Assurer la gestion des laboratoires, pépinières, parcelles expérimentales, parcelles d\'essais génétiques ainsi que les arboretums 
- Assurer l\'encadrement des unités de recherche thématiques',
                'type' => 'central',
            ],
            244 => [
                'description' => '- Parachever les projets de caractérisation physico-mécaniques des bois d\'espèces forestières et promouvoir des procédés innovants de transformation et de développement des chaînes de valeurs 
- Planifier et encadrer la réalisation des projets de recherche-développement dans les domaines de biomasse énergie, efficacité énergétiques, valorisation des plantes aromatiques et médicinales, de qualification de liège et PFNL (résines, Biochar, HE, anti-oxydants…) 
- Promouvoir des formes de partenariats institutionnels et privés autour des projets de recherches incubés dans le service 
- Assurer la gestion des laboratoires et de l\'unité industrielle du bois 
- Assurer l\'encadrement des unités de recherche et concourir à la vulgarisation des résultats de recherche',
                'type' => 'central',
            ],
            245 => [
                'description' => '- Planifier à moyen terme des projets de recherches relatifs aux études écologiques, biogéographiques et des populations d\'espèces cynégétiques et sauvages et en assurer la réalisation et le suivi 
- Capitaliser sur les acquis de recherche en érosion hydrique des sols, des dynamiques d\'érosion éoliennes et des techniques de lutte 
- Promouvoir des solutions techniques innovantes en termes de restauration des écosystèmes forestiers et de lutte contre l\'érosion (hydrique et éolienne) 
- Assurer l\'encadrement des unités de recherches et concourir à la vulgarisation des résultats de recherche.',
                'type' => 'central',
            ],
            246 => [
                'description' => '- Identifier les déficits de compétence du capital humain de l\'ANEF 
- Elaborer et mettre en œuvre le programme de formation au niveau central, appuyer et encadrer les services régionaux concernés dans l\'élaboration, la mise en œuvre et l\'évaluation des plans de formation regionaux 
- Gérer les marchés relatifs à la formation au niveau central et déléguer les crédits pour la réalisation des plans de formation regionaux 
- Evaluer le plan de formation et élaborer le bilan annuel des réalisations en matière de formation par l\'ANEF à l\'échelle nationale (central et régional) 
- Gérer les dossiers d\'envoi en stage de formation (de longue durée) et de perfectionnement (courte durée) au Maroc et à l\'étranger 
- Gérer les demandes de stage des étudiants des établissements de formation',
                'type' => 'central',
            ],
        ];

        foreach ($entiteInfosData as $entiteId => $infoData) {
            EntiteInfo::updateOrCreate(
                ['entite_id' => $entiteId],
                $infoData
            );
        }

        // Update lieu_affectation for all entities
        $this->updateLieuAffectation();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Successfully seeded ' . count($entitiesData) . ' entities and ' . count($entiteInfosData) . ' entite_infos.');
    }

    /**
     * Update lieu_affectation for all entities based on their names and parent relationships
     */
    private function updateLieuAffectation(): void
    {
        $this->command->info('Updating lieu_affectation for all entities...');
        
        // Map of city names (uppercase) to proper case
        $cityMap = [
            'TANGER' => 'Tanger',
            'TETOUAN' => 'Tétouan',
            'AL HOCEIMA' => 'Al Hoceima',
            'HOCEIMA' => 'Al Hoceima',
            'FAHS-ANJRA' => 'Fahs-Anjra',
            'FAHS ANJRA' => 'Fahs-Anjra',
            'MDIQ-FNIDEQ' => 'Mdiq-Fnideq',
            'MDIQ FNIDEQ' => 'Mdiq-Fnideq',
            'LARACHE' => 'Larache',
            'CHEFCHAOUEN' => 'Chefchaouen',
            'OUAZZANE' => 'Ouezzane',
            'OUJDA' => 'Oujda',
            'NADOR' => 'Nador',
            'DRIOUCHE' => 'Driouche',
            'BERKANE' => 'Berkane',
            'TAOURIRT' => 'Taourirt',
            'JERADA' => 'Jerada',
            'FIGUIG' => 'Figuig',
            'RABAT' => 'Rabat',
            'SALE' => 'Salé',
            'SALÉ' => 'Salé',
            'SKHIRAT' => 'Skhirat',
            'TEMARA' => 'Témara',
            'TÉMARA' => 'Témara',
            'KENITRA' => 'Kénitra',
            'KÉNITRA' => 'Kénitra',
            'SIDI KACEM' => 'Sidi Kacem',
            'SIDI SLIMANE' => 'Sidi Slimane',
            'SIDI YAHYA' => 'Sidi Yahya',
            'CASABLANCA' => 'Casablanca',
            'MOHAMMEDIA' => 'Mohammédia',
            'MOHAMMÉDIA' => 'Mohammédia',
            'NOUACEUR' => 'Nouaceur',
            'MEDIOUNA' => 'Médiouna',
            'MÉDIOUNA' => 'Médiouna',
            'BENSlimane' => 'Benslimane',
            'BENSLIMANE' => 'Benslimane',
            'BERRECHID' => 'Berrechid',
            'EL JADIDA' => 'El Jadida',
            'SETTAT' => 'Settat',
            'FES' => 'Fès',
            'FÈS' => 'Fès',
            'MEKNES' => 'Meknès',
            'MEKNÈS' => 'Meknès',
            'SEFROU' => 'Sefrou',
            'BOULMANE' => 'Boulemane',
            'BOULEMANE' => 'Boulemane',
            'TAOUNATE' => 'Taounate',
            'TAZA' => 'Taza',
            'IFRANE' => 'Ifrane',
            'MARRAKECH' => 'Marrakech',
            'CHICHAOUA' => 'Chichaoua',
            'EL KELAA' => 'El Kelaa des Sraghna',
            'KELAA' => 'El Kelaa des Sraghna',
            'ESSAOUIRA' => 'Essaouira',
            'REHAMNA' => 'Rehamna',
            'AL HAOUZ' => 'Al Haouz',
            'HAOUZ' => 'Al Haouz',
            'OUARZAZATE' => 'Ouarzazate',
            'ZAGORA' => 'Zagora',
            'TINEGHIR' => 'Tinghir',
            'TINGHIR' => 'Tinghir',
            'AGADIR' => 'Agadir',
            'TAROUDANT' => 'Taroudant',
            'TIZNIT' => 'Tiznit',
            'Chtouka' => 'Chtouka-Aït Baha',
            'CHTOUKA' => 'Chtouka-Aït Baha',
            'TATA' => 'Tata',
            'INEZGANE' => 'Inezgane-Aït Melloul',
            'LAYOUNE' => 'Laâyoune',
            'LAAYOUNE' => 'Laâyoune',
            'BOUJDOUR' => 'Boujdour',
            'TARFAYA' => 'Tarfaya',
            'ES-SEMARA' => 'Es-Smara',
            'SMARA' => 'Es-Smara',
            'DAKHLA' => 'Dakhla',
            'OUED ED-DAHAB' => 'Oued Ed-Dahab',
            'GUELMIM' => 'Guelmim',
            'ASSA-ZAG' => 'Assa-Zag',
            'ASSA ZAG' => 'Assa-Zag',
            'TANTAN' => 'Tantan',
            'SIDI IFNI' => 'Sidi Ifni',
            'AOUSSERD' => 'Aousserd',
            'ERRACHIDIA' => 'Errachidia',
            'MIDELT' => 'Midelt',
            'BENI MELLAL' => 'Beni Mellal',
            'AZILAL' => 'Azilal',
            'KHENIFRA' => 'Khenifra',
            'KHOURIBGA' => 'Khouribga',
        ];

        // Regional direction to main city mapping
        $regionalCityMap = [
            'TANGER TÉTOUAN AL HOCEIMA' => 'Tanger',
            'TANGER TETOUAN AL HOCEIMA' => 'Tanger',
            'ORIENTAL' => 'Oujda',
            'RABAT-SALÉ-KÉNITRA' => 'Rabat',
            'RABAT-SALÉ-KENITRA' => 'Rabat',
            'RABAT SALÉ KÉNITRA' => 'Rabat',
            'RABAT SALÉ KENITRA' => 'Rabat',
            'CASABLANCA-SETTAT' => 'Casablanca',
            'CASABLANCA SETTAT' => 'Casablanca',
            'FÈS-MEKNÈS' => 'Fès',
            'FES-MEKNES' => 'Fès',
            'FÈS MEKNÈS' => 'Fès',
            'FES MEKNES' => 'Fès',
            'MARRAKECH-SAFI' => 'Marrakech',
            'MARRAKECH SAFI' => 'Marrakech',
            'DRAA-TAFILALET' => 'Ouarzazate',
            'DRAA TAFILALET' => 'Ouarzazate',
            'SOUSS-MASSA' => 'Agadir',
            'SOUSS MASSA' => 'Agadir',
            'LAAYOUNE-SAKIA EL HAMRA' => 'Laâyoune',
            'LAAYOUNE SAKIA EL HAMRA' => 'Laâyoune',
            'DAKHLA-OUED ED-DAHAB' => 'Dakhla',
            'DAKHLA OUED ED-DAHAB' => 'Dakhla',
            'GUELMIM OUED NOUN' => 'Guelmim',
            'BÉNI MELLAL' => 'Beni Mellal',
            'BENI MELLAL' => 'Beni Mellal',
        ];

        // Central entities default to Rabat
        $centralKeywords = ['DIRECTEUR GÉNÉRAL', 'DIRECTEUR GENERAL', 'SECRÉTAIRE GÉNÉRAL', 'SECRETAIRE GENERAL', 'CENTRE INNOVATION', 'DÉPARTEMENT', 'SERVICE'];

        $entities = Entite::all();
        $updated = 0;
        $defaultCity = 'Rabat'; // Default for central entities

        // First pass: Extract city from entity name
        foreach ($entities as $entity) {
            $lieuAffectation = null;
            $nameUpper = strtoupper($entity->name);

            // Check if entity name contains a city name
            foreach ($cityMap as $cityUpper => $cityProper) {
                if (strpos($nameUpper, $cityUpper) !== false) {
                    $lieuAffectation = $cityProper;
                    break;
                }
            }

            // If not found, check regional directions
            if (!$lieuAffectation) {
                foreach ($regionalCityMap as $regionUpper => $city) {
                    if (strpos($nameUpper, $regionUpper) !== false) {
                        $lieuAffectation = $city;
                        break;
                    }
                }
            }

            // Update if we found a city
            if ($lieuAffectation) {
                $entity->lieu_affectation = $lieuAffectation;
                $entity->save();
                $updated++;
            }
        }

        // Second pass: Inherit from parent (recursively)
        $maxIterations = 10; // Prevent infinite loops
        $iteration = 0;
        while ($iteration < $maxIterations) {
            $foundNew = false;
            foreach ($entities as $entity) {
                if (!$entity->lieu_affectation && $entity->parent_id) {
                    $parent = Entite::find($entity->parent_id);
                    if ($parent && $parent->lieu_affectation) {
                        $entity->lieu_affectation = $parent->lieu_affectation;
                        $entity->save();
                        $foundNew = true;
                        $updated++;
                    }
                }
            }
            if (!$foundNew) {
                break;
            }
            $iteration++;
        }

        // Third pass: Set default for remaining entities (central entities)
        foreach ($entities as $entity) {
            if (!$entity->lieu_affectation) {
                $nameUpper = strtoupper($entity->name);
                $isCentral = false;
                foreach ($centralKeywords as $keyword) {
                    if (strpos($nameUpper, $keyword) !== false) {
                        $isCentral = true;
                        break;
                    }
                }
                $entity->lieu_affectation = $isCentral ? $defaultCity : $defaultCity;
                $entity->save();
                $updated++;
            }
        }

        // For regional directions, also set lieu_direction
        foreach ($entities as $entity) {
            $nameUpper = strtoupper($entity->name);
            if (strpos($nameUpper, 'DIRECTIONS REGIONALES') !== false || 
                strpos($nameUpper, 'DIRECTION REGIONALE') !== false) {
                if ($entity->lieu_affectation) {
                    $entity->lieu_direction = $entity->lieu_affectation;
                    $entity->save();
                }
            }
        }

        // Ensure lieu_direction is set for all entities (inherit from lieu_affectation if not set)
        foreach ($entities as $entity) {
            if (!$entity->lieu_direction && $entity->lieu_affectation) {
                $entity->lieu_direction = $entity->lieu_affectation;
                $entity->save();
            } elseif (!$entity->lieu_direction) {
                // If still null, set to default
                $entity->lieu_direction = $defaultCity;
                $entity->save();
            }
        }

        $this->command->info("Updated lieu_affectation and lieu_direction for {$updated} entities.");
    }
}
