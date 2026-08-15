<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $services = [
            [
                'title' => 'Enregistrement des Conducteurs',
                'description' => 'Inscription et gestion des cartes professionnelles pour conducteurs agréés.',
                'icon' => 'id-card',
                'color' => '#007FFF',
                'link' => '/services',
            ],
            [
                'title' => 'Immatriculation des Véhicules',
                'description' => 'Enregistrement et suivi des véhicules de transport public et privé.',
                'icon' => 'car',
                'color' => '#CE1021',
                'link' => '/services',
            ],
            [
                'title' => 'Paiement des Taxes',
                'description' => "Acquittement des taxes de circulation et d'exploitation en ligne.",
                'icon' => 'credit-card',
                'color' => '#e6a800',
                'link' => '/services',
            ],
            [
                'title' => 'Vérification en Ligne',
                'description' => 'Vérification de la validité des cartes, permis et documents officiels.',
                'icon' => 'shield-check',
                'color' => '#28A745',
                'link' => '/services',
            ],
        ];

        $stats = [
            ['value' => '15000+', 'label' => 'Conducteurs enregistrés', 'icon' => 'users'],
            ['value' => '8500+',  'label' => 'Véhicules immatriculés',  'icon' => 'truck'],
            ['value' => '12000+', 'label' => 'Cartes délivrées',        'icon' => 'file-text'],
            ['value' => '24/7',   'label' => 'Services en ligne',       'icon' => 'globe'],
        ];

        $actualites = [
            [
                'titre' => "Campagne d'enregistrement des conducteurs 2024",
                'date' => '15 Mars 2024',
                'resume' => "Le ministère lance une campagne massive d'enregistrement des drivers professionnels sur toute l'étendue de la province.",
                'tag' => 'Annonce',
            ],
            [
                'titre' => 'Nouveau système de paiement des taxes',
                'date' => '10 Mars 2024',
                'resume' => 'De nouveaux modes de paiement sont désormais disponibles : mobile money, carte bancaire et virement.',
                'tag' => 'Mise à jour',
            ],
            [
                'titre' => 'Inauguration du nouveau terminal de transport',
                'date' => '05 Mars 2024',
                'resume' => "Le ministre a procédé à l'inauguration du nouveau terminal moderne de transport en commun.",
                'tag' => 'Événement',
            ],
            [
                'titre' => 'Réglementation des taxis-motos en 2024',
                'date' => '01 Mars 2024',
                'resume' => "De nouvelles règles encadrent désormais la circulation des taxis-motos dans la province pour plus de sécurité.",
                'tag' => 'Annonce',
            ],
            [
                'titre' => 'Partenariat avec les assureurs locaux',
                'date' => '25 Fév 2024',
                'resume' => "Le ministère signe un accord avec plusieurs compagnies d'assurance pour faciliter les démarches des transporteurs.",
                'tag' => 'Événement',
            ],
        ];

        $this->render('home/index', [
            'pageTitle' => 'Accueil',
            'currentPage' => '/',
            'services' => $services,
            'stats' => $stats,
            'actualites' => $actualites,
        ]);
    }
}
