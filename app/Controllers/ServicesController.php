<?php

namespace App\Controllers;

use App\Core\Controller;

class ServicesController extends Controller
{
    public function index()
    {
        $services = [
            [
                'title' => 'Enregistrement des Conducteurs',
                'description' => 'Inscrivez-vous en tant que conducteur professionnel et obtenez votre carte professionnelle officielle.',
                'icon' => 'id-card',
                'color' => '#007FFF',
                'details' => ['Carte professionnelle', 'Renouvellement annuel', 'Suivi en ligne'],
            ],
            [
                'title' => 'Immatriculation des Véhicules',
                'description' => 'Enregistrez et gérez vos véhicules de transport en toute simplicité.',
                'icon' => 'car',
                'color' => '#CE1021',
                'details' => ["Plaque d'immatriculation", 'Certificat de conformité', 'Suivi du véhicule'],
            ],
            [
                'title' => 'Paiement des Taxes',
                'description' => "Acquittez vos taxes de circulation et d'exploitation directement en ligne.",
                'icon' => 'credit-card',
                'color' => '#FCD116',
                'details' => ['Taxe de circulation', "Taxe d'exploitation", 'Reçus numériques'],
            ],
            [
                'title' => 'Vérification en Ligne',
                'description' => 'Vérifiez la validité de vos documents et permis de transport en temps réel.',
                'icon' => 'shield-check',
                'color' => '#28A745',
                'details' => ['Validité des cartes', 'Statut des permis', 'Historique des paiements'],
            ],
            [
                'title' => 'Permis de Transport',
                'description' => 'Demandez et renouvelez vos permis de transport de personnes ou de marchandises.',
                'icon' => 'file-text',
                'color' => '#6F42C1',
                'details' => ['Transport de personnes', 'Transport de marchandises', 'Permis spéciaux'],
            ],
            [
                'title' => 'Support & Assistance',
                'description' => 'Notre équipe est disponible pour vous accompagner dans toutes vos démarches.',
                'icon' => 'headphones',
                'color' => '#FD7E14',
                'details' => ['Chat en direct', 'FAQ', 'Rendez-vous en ligne'],
            ],
        ];

        $this->render('services/index', [
            'pageTitle' => 'Nos Services',
            'currentPage' => '/services',
            'services' => $services,
        ]);
    }
}
