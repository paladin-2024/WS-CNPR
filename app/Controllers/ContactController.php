<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = [
            ['icon' => 'map-pin', 'color' => '#CE1021', 'label' => 'Adresse',   'value' => 'Boulevard du 30 Juin, Kinshasa, RDC'],
            ['icon' => 'phone',   'color' => '#007FFF', 'label' => 'Téléphone', 'value' => '+243 81 000 0000'],
            ['icon' => 'mail',    'color' => '#28A745', 'label' => 'Email',     'value' => 'contact@mintransport.gov.cd'],
            ['icon' => 'clock',   'color' => '#FCD116', 'label' => 'Horaires',  'value' => 'Lun–Ven, 08h00–16h00'],
        ];

        $faq = [
            [
                'question' => 'Comment obtenir ma carte de conducteur ?',
                'answer' => "Créez un compte, remplissez le formulaire d'enregistrement et soumettez vos documents. Votre carte sera traitée sous 5 jours ouvrables.",
            ],
            [
                'question' => 'Comment payer mes taxes en ligne ?',
                'answer' => 'Connectez-vous à votre espace, accédez à "Paiement des taxes" et choisissez votre mode de paiement (mobile money, carte bancaire).',
            ],
            [
                'question' => 'Comment vérifier un document ?',
                'answer' => "Utilisez le service \"Vérification en ligne\", entrez le numéro du document ou la plaque d'immatriculation.",
            ],
            [
                'question' => "Combien de temps pour l'immatriculation ?",
                'answer' => "Le traitement d'une demande d'immatriculation prend entre 3 et 7 jours ouvrables selon la complétude du dossier.",
            ],
        ];

        $flashData = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $flash = is_array($flashData) ? ($flashData['message'] ?? null) : $flashData;

        $this->render('contact/index', [
            'pageTitle' => 'Contact',
            'currentPage' => '/contact',
            'contacts' => $contacts,
            'faq' => $faq,
            'flash' => $flash,
        ]);
    }

    public function send()
    {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sujet = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Veuillez remplir tous les champs.'];
            $this->redirect('/contact');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Adresse email invalide.'];
            $this->redirect('/contact');
            return;
        }

        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO contact_messages (nom, email, sujet, message, date_envoi) VALUES (?, ?, ?, ?, NOW())",
                [$nom, $email, $sujet, $message]
            );

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre message a été envoyé avec succès !'];
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => "Erreur lors de l'envoi du message. Veuillez réessayer."];
        }

        $this->redirect('/contact');
    }
}
