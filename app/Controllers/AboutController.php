<?php

namespace App\Controllers;

use App\Core\Controller;

class AboutController extends Controller
{
    public function index()
    {
        $missions = [
            [
                'icon' => 'shield',
                'title' => 'Sécurité Routière',
                'description' => "Garantir la sécurité de tous les usagers de la route par une réglementation stricte et un contrôle régulier.",
                'color' => '#007FFF',
            ],
            [
                'icon' => 'target',
                'title' => 'Régulation du Transport',
                'description' => "Encadrer et organiser les activités de transport public et privé sur toute l'étendue de la province.",
                'color' => '#CE1021',
            ],
            [
                'icon' => 'users',
                'title' => 'Service aux Citoyens',
                'description' => 'Offrir des services administratifs accessibles, rapides et dématérialisés pour faciliter les démarches.',
                'color' => '#FCD116',
            ],
            [
                'icon' => 'award',
                'title' => 'Qualité & Standards',
                'description' => 'Veiller à la conformité des véhicules et des conducteurs aux normes nationales et internationales.',
                'color' => '#28A745',
            ],
        ];

        $valeurs = [
            'Transparence',
            'Intégrité',
            'Excellence',
            'Service public',
            'Innovation',
            'Responsabilité',
        ];

        $equipe = [
            ['nom' => 'Ministre Provincial', 'poste' => 'Direction Générale',          'initiales' => 'MP'],
            ['nom' => 'Secrétaire Général',  'poste' => 'Administration',              'initiales' => 'SG'],
            ['nom' => 'Direction Technique',  'poste' => 'Immatriculation & Permis',    'initiales' => 'DT'],
            ['nom' => 'Direction Fiscale',    'poste' => 'Taxes & Redevances',          'initiales' => 'DF'],
        ];

        $contacts = [
            ['icon' => 'map-pin', 'color' => '#CE1021', 'label' => 'Adresse',   'value' => 'Boulevard du 30 Juin, Kinshasa, RDC'],
            ['icon' => 'phone',   'color' => '#007FFF', 'label' => 'Téléphone', 'value' => '+243 81 000 0000'],
            ['icon' => 'mail',    'color' => '#28A745', 'label' => 'Email',     'value' => 'contact@mintransport.gov.cd'],
        ];

        $institutionFacts = [
            'Fondé conformément au décret provincial n°001/2010',
            'Siège à Kinshasa, RD Congo',
            'Services numériques depuis 2023',
        ];

        $quickStats = [
            ['value' => '15K+', 'label' => 'Conducteurs'],
            ['value' => '8.5K+', 'label' => 'Véhicules'],
            ['value' => '12K+', 'label' => 'Cartes'],
            ['value' => '24/7', 'label' => 'En ligne'],
        ];

        $this->render('about/index', [
            'pageTitle' => 'À propos',
            'currentPage' => '/about',
            'missions' => $missions,
            'valeurs' => $valeurs,
            'equipe' => $equipe,
            'contacts' => $contacts,
            'institutionFacts' => $institutionFacts,
            'quickStats' => $quickStats,
        ], 'main');
    }
}
