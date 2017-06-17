<?php
// item 0 : label
// item 1 : URL
// item 2 : open URL in a new window
$extra = array(
    array(
        "Liste des Vigiks",
        site_url('newvigik'),
        false),
    array(
        "Villes à sous-traiter",
        site_url('newsous_traitants'),
        false),
    array(
        "Gestion Serveurs",
        "#",
        array(
            array(
                "Serveurs",
                site_url('servers'),
                false
            ),
            array(
                "Hebergeur",
                site_url('hosts'),
                false
            ),
            array(
                "Propriétaire",
                site_url('owners'),
                false
            ),
            array(
                "Domains",
                site_url('domains'),
                false
            ),
            array(
                "IPS",
                site_url('ips'),
                false
            ),
			array(
                "Cartes Bleues",
                site_url('cartes_blues'),
                false
            ),
        )
    ),
    array(
        "E-mailing",
        "#",
        array(
            array(
                "Providers",
                site_url('providers'),
                false
            ),
            array("Production mails",
                site_url('production_mails'),
                false
            ),
            array("Softwares",
                site_url('softwares'),
                false
            ),
            array(
                "E-mailing Statistiques",
                "#",
                array(
                    array(
                        "Detailed Statistics Result",
                        site_url('mail_log_analyzer'),
                        false
                    ),
                    array(
                        "Summarize Result",
                        site_url('mail_log_analyzer/summarize'),
                        false
                    ),
                )
            ),
            array("Liste des Messages",
                site_url('message_list'),
                false
            ), 
            array(
                "RBLs",
                site_url('rbl_list'),
                false
            ),
            array(
                "Liste globale des envois",
                site_url('global_list'),
                false
            ),
            array(
                "Envois emailing",
                site_url('emailing'),
                false
            ),
            array(
                "Envois manuels",
                site_url('manual_sending'),
                false
            ),
            array(
                "Envois maxbulk",
                site_url('max_bulk'),
                false
            ),
            array(
                "Envois pages jaunes",
                site_url('pages_jaunes'),
                false
            ),
            array(
                "Envois openemm",
                site_url('openemm'),
                false
            ), 
			array(
                "Envois Sendgrid",
                site_url('sendgrid'),
                false
            ), 			
			array(
                "Envois Sendinblue",
                site_url('sendinblue'),
                false
            ),
            array(
                " Envois Airmail",
                site_url('airmail'),
                false
            ),
            array(
                " Envois Mailchimp",
                site_url('mailchimp'),
                false
            ),
        )
    ),	   	
    array(
        "Base de données contacts",
        "http://contact.bal-idf.com",
        true),    
    array(
        "Owncloud GED",
        site_url('ged'),
        false),	
    array(
        "Suivi des Véhicules",
        site_url('vehicules'),
        false),
    array(
        "Livraisons",
        site_url('livraisons'),
        false),
    array(
        "Liste des Amalgames",
        site_url('amalgame'),
        false),
    array(
        "Lignes téléphoniques",
        site_url('telephones'),
        false),   
	array(
		"Contrôles Récurrents",
		site_url('controle_recurrents'),
		false),				
	array("Comptes-rendus Salariés",
		site_url('gestion_heures'),
		false),    
    array("Objectifs",
        site_url('objectif'),
        false),    
    array("Contrôle de Distribution",
        site_url('feuille_controle'),
        false),
    array("Suivi Adwords",
        site_url('suivi_adwords'),
        false),
    array("Factures à Récupérer Compta",
        site_url('factures_compta'),
        false),
    array("Developpers Followup",
        site_url('developpers_followup'),
        false),
	array("Tests Followup",
        site_url('tests_followup'),
        false)
);