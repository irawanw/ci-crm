<?php
$menus = array(
    'nom'=>"CRM",
    'gauche' => array(
        "Contacts" => array(
            "Liste" => 'contacts',
            "Gestion de fichiers" => 'contacts/fichiers',
			"Document templates" => 'document_templates',
            "Liste du document de contact" => 'contact_document_files'
        ),
        "Produits" => array(
            "Catalogues" => 'catalogues',
            "Ajouter catalogue" => 'catalogues/nouveau'
        ),
        "Ventes" => array(
            "Promotions" => 'promotions',
            "Devis" => 'devis',
            "Commandes" => 'commandes',
            "Factures" => 'factures',
            "Avoirs" => 'avoirs',
            "Règlements" => 'reglements',
            "Interface comptable" => 'interface_comptable',
            "Enseignes" => 'societes_vendeuses',
            "Taux de TVA" => 'taux_tva',
            "Tableaux de bord" => 'tableaux_bord',
        	"Statistiques Prospection" => 'statistiques_prospection',
        	"Suivi demandes de devis" => array(
                "Liste complète des demandes" => 'demande_devis_general',
                "Comparaison entre commerciaux" => 'demande_devis_commerciaux_dt',
                "Vues synthétiques" => 'demande_devis_quick_followup'          
            ),
            "Prix Unitaire Setting" => 'articles_distribution_base_price',
            "Prix Percentage Setting" => 'articles_distribution_price'
        ),
        "Production" => array(
            "Ordres de production" => 'ordres_production',
            "Villes" => 'villes',
            "Plaintes" => 'plaintes',
            "Commandes à trier" => 'commandes_trier',
            "Feuille de Tri" => 'feuilles_de_tri',            
            "Feuilles de Route" => "feuille_de_route",
        ),
        "Espace client" => array(
            "Commandes" => 'commandes/commandes_escli/1',
            "Mon compte" => 'correspondants/mon_compte/1'
        ),
        "GED" => array(
            "Factures fournisseurs" => 'documents_factures',
            "Autres documents" => 'documents_autres',
            "Modèles de documents" => 'modeles_documents',
            "Champs de fusion" => 'modeles_documents/champs_fusion',
            "Documents générés" => 'modeles_documents/documents_generes',
            "Boites archive" => 'boites_archive',
            "Disques d'archivage" => 'disques_archivage'
        ),
        "Agenda" => array(
            "Alertes" => 'alertes',
            "Tâches" => 'taches',
            "Achats" => 'purchases'
        ),
        "Utilisateurs" => array(            
            "Liste des utilisateurs" => 'list_utilisateurs',
			"Users Permissions" => 'users_permissions/settings',
        )
    ),
    'droit' => array(
        "Connexion" => 'utilisateurs/login',
        "Déconnexion" => 'utilisateurs/logout'
    ),
    'toolbar' => array(
        "Alertes" => 'alertes',
        "Tâches" => array(
            "Affectées" => 'taches/affectees',
            "Sous-traitées" => 'taches/sous_traitees',
            "Nouvelle tâche" => 'taches/nouveau'
        ),
        "Messages" => array(
            "Reçus" => 'messages/recus',
            "Envoyés" => 'messages/emis',
            "Nouveau message" => 'messages/nouveau'
        )
    ),
    'couleurs_toolbar' => array('Tomato','DarkTurquoise','LightGreen'),
    'indicateurs_toolbar' => array('nouvelles_alertes','nouvelles_taches','messages_non_lus'),
    'lateral' => array(
        "Menu 1" => array(
            "Action 1" => array('#',array("Administrateur","Commercial")),
            "Action 2" => array('#',array("Administrateur","Commercial")),
            "Action 3" => array('#',array("Administrateur","Commercial")),
            "Action 4" => array('#',array("Administrateur","Commercial"))
        ),
        "Menu 2" => array(
            "Action 5" => array('#',array("Administrateur","Commercial")),
            "Action 6" => array('#',array("Commercial")),
            "Action 7" => array('#',array("Administrateur"))
        )
    )
);