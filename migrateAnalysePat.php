<?php

class MigrateAnalysePat extends Controller
{

    public $all_costumer = array();
    public function __construct()
    {
        parent::__construct();
        // Auth::handleLogin();
    }

    function getCustomerIdFromJSON() {
        // a remplacer par la database du crm
        $mysqli = new mysqli('localhost:3306', 'root', 'root', 'crm');
        //$mysqli = new mysqli('localhost', 'crm', 'Wrb0SvM74IoS4wAA', 'crm');

        $createCopieClient = "CREATE TABLE IF NOT EXISTS copie_customer_crm  (
            `id` int(11) UNSIGNED NOT NULL,
            `login_user` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
            `id_spouse` int(11) UNSIGNED DEFAULT NULL,
            `title` enum('mr','mme','mr_mme') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'mr',
            `lastname` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
            `firstname` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
            `phone` varchar(16) COLLATE utf8_swedish_ci DEFAULT NULL,
            `mobile_phone` varchar(16) COLLATE utf8_swedish_ci DEFAULT NULL,
            `subscribe` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `address` varchar(128) COLLATE utf8_swedish_ci DEFAULT NULL,
            `city` varchar(64) COLLATE utf8_swedish_ci DEFAULT NULL,
            `zip_code` varchar(10) COLLATE utf8_swedish_ci DEFAULT NULL,
            `mail` varchar(64) COLLATE utf8_swedish_ci DEFAULT NULL,
            `taxe_type` enum('habiter','0-2500','2500-5000','5000-10000','10000+') COLLATE utf8_swedish_ci NOT NULL DEFAULT '0-2500',
            `comment` text COLLATE utf8_swedish_ci,
            `id_user` int(11) UNSIGNED DEFAULT NULL,
            `id_marketing` varchar(128) COLLATE utf8_swedish_ci DEFAULT NULL,
            `pot_commun` int(11) NOT NULL DEFAULT '0',
            `date` datetime DEFAULT NULL,
            `imported` tinyint(1) NOT NULL DEFAULT '0',
            `reaffect_date` datetime DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci; ";
        $reqCreateCopieClient =  $mysqli->query($createCopieClient);

        $type = ['invest', 'rp'];
        
        $results = array();
        
        foreach($type as $t) {
            $directory = ROOT.'/data/assetsAnalysis/'.$t.'/';
    
            $files = glob($directory . '/*');
            $pattern = '/.*_(\d+).json/';

            $matches = array();
            foreach ($files as $file) {

                preg_match($pattern, $file, $matches);
                //verifie qu'il n'y est pas d'id en double
                if ( !in_array($matches[1], $results) ) {
                    if ($file != ROOT.'/data/assetsAnalysis/invest//invest_.json' && $file != ROOT.'/data/assetsAnalysis/rp//rp_.json') {

                        $insert_customer_copie = "INSERT INTO `copie_customer_crm` SELECT  * FROM `customer`  WHERE id = $matches[1] ;";
                        $reqInsert_customer_copie = $mysqli->query( $insert_customer_copie );
                        if ($mysqli->error) {
                            error_log($matches[1]);
                            error_log("requette => $insert_customer_copie");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;
                        }
                        array_push($results, $matches[1]);
                    }

                }
            }
        }
        error_log(COUNT($results));
        error_log('fin de transfert => copie customer crm');
    }

    public function getJointureConsultant() {
        $mysqli_backoffice =  new mysqli('localhost:3306', 'root', 'root', 'backoffice');
        //$mysqli_backoffice = new mysqli('localhost', 'backoffice', 'vGaO0Y1hb58twcZV', 'backoffice');


        $select_copie_consultant_crm = "SELECT * FROM `copie_consultant_crm` WHERE id  IN (5,1,288,372)";
//        $reqSelect_copie_consultant_crm = $mysqli_backoffice->query($select_copie_consultant_crm)->fetch_all(MYSQLI_ASSOC);
        $testData = $mysqli_backoffice->query($select_copie_consultant_crm);

        $reqSelect_copie_consultant_crm = [];
        while ($row = $testData->fetch_assoc()) {
            $reqSelect_copie_consultant_crm[] = $row;
        }

        foreach ( $reqSelect_copie_consultant_crm as $key => $value) {
           $lastName = $value['lastname'];
           $firstName = $value['firstname'];
           $avatar = $value['base.png'];
           $role = $value['role'];
           $mobil_phone = $value['mobile_phone'];
           $login = $value['login'];
           $mail = $value['mail'];

           $insert_nov_collaborateur_table = "INSERT INTO `collaborateur_table`( `saisi_fiche`, `etat_candidature`, `etat`, `civilite`, `first_name`, `last_name`,
           `ville`, `poste`, `image`, `birthdate`, `birthcity`, `telephone`, `telephone_pro`, 
           `email_perso`, `adresse_perso`, `ville_perso`, `cp_perso`, `longitude`, `latitude`, 
           `cv_file`, `recrut_origine`, `recrut_manager_novanea`, `recrut_parrain`, 
           `recrut_parrain_novanea`, `recrut_autre`, `parrain_email`, `parrain_phone`,
           `parrain_nom`, `parrain_prenom`, `prise_de_rdv_json`, `rdv1`, `rdv2`, `rdv3`, 
           `rdv1_media`, `rdv2_media`, `rdv3_media`, `manager`, `contrat_type`, 
           `pre_entreenovanea`, `entreenovanea`, `fin_novanea`, `contrat`, `salaire`, 
           `rem_7030`, `rem_1`, `rem_2`, `rem_3`, `heure_travail`, `formation_date_debut`, 
           `formation_date_fin`, `formation_lieu`, `formation_promo`, `formation_diplome`, 
           `salarie_contrat`, `salarie_essai`, `salarie_essai2`, `email_novanea`, 
           `pwd_novanea`, `user_email`, `user_pass`, `id_crm`, `pwd_crm`, `id_altoffice`, 
           `pwd_altoffice`, `alur_annee_1`, `alur_validite_1`, `alur_attestation_1`, 
           `carte_t_type`, `carte_t_annee_1`, `carte_t_validite_1`, `carte_t_attestation_1`, 
           `habilitation`, `rem_vente`, `rem_reco`, `rem_parrainage`, `cle_remise`, `cle_restitution`,
           `badge_remise`, `badge_restitution`, `icloud_mp`, `icloud_email`, `tel_type`, `tel_novanea`, 
           `tel_imei`, `tel_pin`, `tel_puk`, `tel_code`, `ordi_type`, `ordi_serie`, `ordi_code`, 
           `inscription_novanea`, `inscription_novanea_statut`, `bureau_partage`, `doc_cni`, `doc_casier`, 
           `doc_carte_vitale`, `doc_rib`, `doc_assurance`, `doc_kbis`, `doc_ccimp`, `doc_sans_salarie`, 
           `doc_rcp`, `date_rcp`, `doc_transaction`, `doc_ursaff`, `date_ursaff`, `doc_impot`, `date_impot`, 
           `doc_permis`, `doc_cartegrise`, `commande_carte_visite`, `commande_carte_visite_livraison`, 
           `commande_guide_qte`, `commande_plaquette_qte`, `commande_parcours_qte`, `commande_parrainage_qte`, 
           `commande_pochette_qte`, `commande_guide_livraison`, `commande_plaquette_livraison`, `commande_parcours_livraison`, 
           `commande_parrainage_livraison`, `commande_pochette_livraison`, `alur_json`, `carte_t_json`, `paye_json`, `facture_json`, 
           `langues_json`, `methode_invest`, `methode_rp`, `cv_saisi`, `annonceur`, `annonce`, `experience_immo`, `experience_com`, 
           `experience_management`, `commentaire`, `msg_motivation`, `msg_motivation_file`, `email_validation`, `validation_manager`,
           `date_validation_manager`, `vu_manager`, `vu_nom_manager`, `appels`, `relance`, `relance_mail`, `joignable`, `date_recrutement`,
           `valide_par_rh`, `date_refus`, `qui_refus`, `raison_refus`, `date_refus_rh`, `qui_refus_rh`, `motif_refus_rh`,
           `validation_manager_json`, `validation_rh_json`, `cloture_qui`, `cloture_motif`, `presence_presentation_formation`, 
           `absence_presentation_cause`, `presence_formation`, `absence_formation_cause`, `avis_candidat`, `motif_debat`, `enregistre`)
           VALUES ('[value-2]','[value-3]','[value-4]','[value-5]','$firstName','$lastName','[value-8]','Consultant',
           '','2023-01-01','[value-12]','[value-13]','$mobil_phone','[value-15]','[value-16]','[value-17]',
           '0','[value-19]','[value-20]','[value-21]','[value-22]','[value-23]','[value-24]','[value-25]','[value-26]',
           '[value-27]','[value-28]','[value-29]','[value-30]','[value-31]','[value-32]','[value-33]','[value-34]',
           '[value-35]','[value-36]','[value-37]','[value-38]','[value-39]','2023-01-01','2023-01-01','2023-01-01',
           '[value-43]','[value-44]','[value-45]','[value-46]','[value-47]','[value-48]','0','2023-01-01','2023-01-01',
           '[value-52]','[value-53]','[value-54]','2023-01-01','2023-01-01','2023-01-01','$mail','aze',
           '$mail','aze','[value-62]','[value-63]','[value-64]','[value-65]','2023-01-01','2023-01-01',
           '[value-68]','[value-69]','2023-01-01','2023-01-01','[value-72]','2023-01-01','[value-74]','0','0','2023-01-01',
           '2023-01-01','2023-01-01','2023-01-01','[value-81]','[value-82]','[value-83]','[value-84]','[value-85]','[value-86]',
           '[value-87]','[value-88]','[value-89]','[value-90]','[value-91]','2023-01-01','[value-93]','[value-94]','[value-95]',
           '[value-96]','[value-97]','[value-98]','[value-99]','[value-100]','[value-101]','[value-102]','[value-103]','2023-01-01',
           '[value-105]','[value-106]','2023-01-01','[value-108]','2023-01-01','[value-110]','[value-111]','[value-112]','2023-01-01',
           '[value-114]','[value-115]','[value-116]','[value-117]','[value-118]','2023-01-01','2023-01-01','2023-01-01','2023-01-01',
           '2023-01-01','[value-124]','[value-125]','[value-126]','[value-127]','[value-128]','[value-129]','[value-130]','0','0','0',
           '[value-134]','[value-135]','[value-136]','[value-137]','[value-138]','[value-139]','[value-140]','[value-141]','2023-01-01',
           '[value-143]','[value-144]','[value-145]','[value-146]','[value-147]','[value-148]','2023-01-01','0','2023-01-01',
           '[value-152]','[value-153]','2023-01-01','[value-155]','[value-156]','[value-157]','[value-158]','[value-159]',
           '[value-160]','[value-161]','[value-162]','[value-163]','[value-164]','[value-165]','[value-166]','2023-01-01')";
           $mysqli_backoffice->query($insert_nov_collaborateur_table);
           if ($mysqli_backoffice->error) {
            error_log($value['id_user']);
            error_log("requette => $insert_nov_collaborateur_table");
            error_log("Échec de la requette: %s\n". $mysqli_backoffice->error);
            exit();
            }
        }

        $create_jointure_customer = "CREATE TABLE jointure_consultant_crm_bo as
                                    SELECT A.ID as id_bo ,B.ID as id_crm FROM `collaborateur_table` as A 
                                    INNER JOIN `copie_consultant_crm` as B 
                                    ON LOWER(A.email_novanea) = LOWER(B.mail) ";
        $mysqli_backoffice->query($create_jointure_customer);

        error_log('fin du transfert consultant crm bo');
    }

    public function getJointureCustomer() {

        $mysqli = new mysqli('localhost:3306', 'root', 'root', 'backoffice');
        //$mysqli = new mysqli('localhost', 'backoffice', 'vGaO0Y1hb58twcZV', 'backoffice');

        $create_jointure_customer = " CREATE TABLE jointure_customer_crm_bo AS
                                    SELECT A.ID as id_crm ,A.id_user as id_consultant_crm ,B.ID as id_bo 
                                    FROM copie_customer_crm as A
                                    INNER JOIN nov_client_table as B 
                                    ON LOWER(A.mail) = LOWER(B.email) AND
                                    LOWER(A.lastname) = LOWER(B.last_name) ;";
        $req_create_jointure_customer = $mysqli->query($create_jointure_customer);

        //requete en boucle sur les consultant
        $select_customer_crm_at_inseret = "SELECT * FROM copie_customer_crm 
        WHERE LOWER(mail) NOT IN (SELECT LOWER(email) FROM nov_client_table )
        OR LOWER(lastname) NOT IN (SELECT LOWER(last_name) FROM nov_client_table );";
        $newData = $mysqli->query($select_customer_crm_at_inseret);


        $req_Select_customer_crm_at_inseret = [];
        while ($row = $newData->fetch_assoc()) {
            $req_Select_customer_crm_at_inseret[] = $row;
        }


        $count = 0;
        foreach ($req_Select_customer_crm_at_inseret as $key => $value) {
        
            $civilite = '';
            if ( $value['title'] != '' ) {
                $civilite = $value['title'] == 'male' ? 'M' : 'Mme';
            }else{
                $civilite = '';   
            }

            $id_consultant = '';
            if ($value['id_user']) {

                //recup le id consultant dans la jointure consultant bo crm
                $select_id_consultant_bo = "SELECT id_bo FROM `jointure_consultant_crm_bo`
                                            WHERE id_crm = ".$value['id_user']." "; 
                $id_consultant = $mysqli->query($select_id_consultant_bo)->fetch_assoc();
            }

            $insert_nov_client_table = "INSERT INTO `nov_client_table` (`civilite`,
                                                                       `last_name`,
                                                                        `first_name`,
                                                                        `adresse`,
                                                                        `cp`,
                                                                        `ville`,
                                                                        `telephone`,
                                                                        `email`, 
                                                                        `date_rdv`, 
                                                                        `societe`, 
                                                                        `statut`, 
                                                                        `date_denonciation`, 
                                                                        `consultant`, 
                                                                        `origine`, 
                                                                        `origine_resa`, 
                                                                        `origine_parrain`, 
                                                                        `origine_espace_client`, 
                                                                        `projet`, 
                                                                        `espace_client`) 
                                            VALUES ('$civilite',
                                                    '".addslashes($value['lastname'])."',
                                                    '".addslashes($value['firstname'])."',
                                                    '".addslashes($value['address'])."',
                                                    '".$value['zip_code']."',
                                                    '".addslashes($value['city'])."',
                                                    '".$value['mobile_phone']."',
                                                    '".$value['mail']."',
                                                    'date_rdv',
                                                    'societe',
                                                    'statut',
                                                     '2022-09-01',
                                                    '".$id_consultant['id_bo']."',
                                                    'origine',
                                                    '".intval('0')."',
                                                    'origine_parrain',
                                                    'origine_espace_client',
                                                    'projet',
                                                    'espace_client')";
            $reqInsert_nov_client_table = $mysqli->query($insert_nov_client_table);
            $id_bo = $mysqli->insert_id;
            if ($mysqli->error) {
                error_log($value['id_user']);
                error_log("requette => $insert_nov_client_table");
                error_log("Échec de la requette: %s\n". $mysqli->error);
                exit();
            }
            $id_crm = $value['id_user'] ?  $value['id_user']  : 'null';
            $insert_jointure_customer_crm = "INSERT INTO `jointure_customer_crm_bo` (id_crm,
                                                                                    id_consultant_crm,
                                                                                    id_bo)
                                            VALUES ('".$value['id']."',
                                                    $id_crm,
                                                    '$id_bo');";
            $reqInsert_jointure_customer_crm = $mysqli->query($insert_jointure_customer_crm );
            if ($mysqli->error) {
                error_log($value['id_user']);
                error_log("requette => $insert_jointure_customer_crm");
                error_log("Échec de la requette: %s\n". $mysqli->error);
                exit();
            }
            //reste une personne non inseret id_crm 114102

            $count++;
        }

        error_log("nb d'insert => $count");            
        error_log('fin de transfert => customer');
    }

    public function transfertDATA() {

        //connexion a la base de donnée 
        function bonneDate($date) {
            $date_reverse = [];
            $date_failed_format = [];
            $date_bon_format = [];
            $pattern_date = '/(^\d{2}\/\d{2}\/\d{4})/'; // si tableaux retourner 
            $pattern_failed_format = '/(^\d{5}-\d{2}-\d{2})/'; // si année a 5 chiffre
            $pattern_bon_format = '/(^\d{4}-\d{2}-\d{2})/'; // si année a 5 chiffre
            preg_match($pattern_date, $date , $date_reverse);
            preg_match($pattern_failed_format, $date , $date_failed_format);
            preg_match($pattern_bon_format, $date , $date_bon_format);
            
            if ( $date_failed_format ) {
                // error_log('@@je suis dans muavais  format @@');
                $date_failed_format = explode('-' , $date_failed_format[1]);
                $date_failed_format[0] =  substr($date_failed_format[0], 0, -1);
                $date_failed_format = implode("-",$date_failed_format);
                return $date =  "'" .  $date_failed_format . "'";
            } else if ($date_reverse) {
                // error_log('@@je suis dans format reverse @@');

                $date_reverse = explode('/' , $date_reverse[1]);
                $date_reverse = array_reverse($date_reverse);
                $date_reverse = implode("-",$date_reverse);
              return  $date =  "'" .  $date_reverse . "'";
            } else if ($date_bon_format) {
                // error_log('@@je suis dans le bon format @@');
               return  "'" . $date . "'";
            } else {
                // error_log('@@je suis dans format null @@');

                return $date =  'null';
                // return $date =  "'" .  '2022-09-01' . "'";
                
            }
        }
        $mysqli = new mysqli('localhost:3306', 'root', 'root', 'backoffice');
        //$mysqli = new mysqli('localhost', 'backoffice', 'vGaO0Y1hb58twcZV', 'backoffice');


        // boucle pour envoyer le scripte sur tout les client 
        
        $type = ['invest', 'rp'];
        $client_not_jointure = [];
        foreach($type as $t) {
            set_time_limit(0);
            $directory = ROOT.'/data/assetsAnalysis/'.$t.'/';
            
            $files = glob($directory . '/*');
            $bug = [];
            $nmb_of_transfert = 0;
            foreach ($files as $file) {

                
                if ($file != ROOT.'/data/assetsAnalysis/invest//invest_.json' && $file != ROOT.'/data/assetsAnalysis/rp//rp_.json' ) {
                    $id_not_a_voir = array();
                    $pattern = '/.*_(\d+).json/';
                    preg_match($pattern, $file , $id_not_a_voir);
                    error_log(" id $t a vérifier => ". $id_not_a_voir[1] ." fichier transfert numéro :  $nmb_of_transfert type : $t");

                    $nmb_of_transfert += 1;
                    $file_json = json_decode(file_get_contents($file));

                    // [0] => invest => 114102
                    // [1] =>  =>
                    // [2] => rp => 114102

                    // //fichier json a exporter du crm au bdd
                    // $file_json = json_decode(file_get_contents("data/assetsAnalysis/invest/invest_115688.json"));
        
                    $assetType = $file_json->assetType;
                    $customerID_crm = $file_json->customerId;

                    //requete sql pour recupérer les jointure client
                    $requeteJointure_customer = "SELECT * FROM `jointure_customer_crm_bo`
                                                WHERE id_crm = $customerID_crm ;";
                    $req_requeteJointure_customer = $mysqli->query($requeteJointure_customer);
                    if ($req_requeteJointure_customer) {
                        $req_requeteJointure_customer =  $req_requeteJointure_customer->fetch_assoc();
                    }
                    
                    $clientID_bo = $req_requeteJointure_customer['id_bo'] ;
                    $idConsultant_crm = $req_requeteJointure_customer['id_consultant_crm'];

                    //requete sql pour recupérer les jointure consultant
                    $requeteJointure_consultant = "SELECT * FROM `jointure_consultant_bo_crm`
                                                WHERE id_crm = $idConsultant_crm ;";
                    $req_requeteJointure_consultant = $mysqli->query($requeteJointure_consultant);
                    if ($req_requeteJointure_consultant != '' ) {
                        $req_requeteJointure_consultant = $req_requeteJointure_consultant->fetch_assoc();
                    }
                    
                    $idCollaborateur_bo = $req_requeteJointure_consultant['id_bo'];
                    if ( $idCollaborateur_bo == '' ) {
                        $idCollaborateur_bo = 0;
                    }
                    
                    //champ pour analyse pat
                    $debut_recherche = intval($file_json->journey->searchMonth);
                    $echeance_analyse = bonneDate($file_json->journey->searchGoal) ;

                    $xp_entourage = $file_json->experience->customer;
                    $parcours_immo = $file_json->experience->circle;
                    $xp_conclusion = $file_json->experience->conclusive;
                    
                    $aime_analyse = $file_json->newProject->likes;
                    $aime_pourquoi_analyse = $file_json->newProject->likesInfo;
                    $aime_pas_analyse = $file_json->newProject->dislikes;
                    $aime_pas_pourquoi_analyse = $file_json->newProject->dislikesInfo;
                    $aimera_analyse = $file_json->newProject->wishes;
                    $aimera_pourquoi_analyse = $file_json->newProject->wishesInfo;
                    
            
                    $ideal_monthly_payment = intval(str_replace(' ', '',$file_json->monthlySavingsFinal));
                    $bring = intval(str_replace(' ', '',$file_json->bringFinal));
                    $borrowing_period = intval(str_replace(' ', '',$file_json->borrowingPeriod));
                    $ideal_budget = intval(str_replace(' ', '',$file_json->idealBudget));
                    $max_budget = intval(str_replace(' ', '',$file_json->maxBudget));

                    $capital_total = $file_json->capitalTotal;
                    $epargneTotale = $file_json->epargneTotale;

                    $housedPetDog = $file_json->housedPetDog  ? $file_json->housedPetDog : 0;
                    $housedPetCat = $file_json->housedPetCat ? $file_json->housedPetCat : 0;
                    $housedPetFish = $file_json->housedPetFish ?  $file_json->housedPetFish : 0;
                    

                    ##-- insert analyse pat --## /OK/ date d'enregistrement
                
                    $insert_nov_analyse = " INSERT INTO `nov_analyse_patrimoniale` (collaborateur,
                                                                                client,
                                                                                type, 
                                                                                debut_recherche,
                                                                                echeance,
                                                                                parcours_immo, 
                                                                                xp_entourage, 
                                                                                xp_conclusion, 
                                                                                epargne_mensuelle, 
                                                                                apport, 
                                                                                duree_emprunt,
                                                                                budget_ideal,
                                                                                budget_max,
                                                                                aime,
                                                                                aime_pourquoi, 
                                                                                aime_pas, 
                                                                                aime_pas_pourquoi, 
                                                                                aimera, 
                                                                                aimera_pourquoi,
                                                                                occupant_logement,
                                                                                enregistre,
                                                                                ideal_monthly_payment,
                                                                                bring,
                                                                                borrowing_period,
                                                                                ideal_budget,
                                                                                max_budget,
                                                                                capital_total,
                                                                                epargne_total,
                                                                                occupant_dog,
                                                                                occupant_cat,
                                                                                occupant_fish)
                            VALUES ($idCollaborateur_bo,
                                    $clientID_bo,
                                    '$assetType',
                                    '$debut_recherche',
                                    $echeance_analyse, 
                                    '".addslashes($parcours_immo)."',
                                    '".addslashes($xp_entourage)."',
                                    '".addslashes($xp_conclusion)."',
                                    0,
                                    0,
                                    0, 
                                    0, 
                                    0, 
                                    '".addslashes($aime_analyse)."', 
                                    '".addslashes($aime_pourquoi_analyse)."', 
                                    '".addslashes($aime_pas_analyse)."', 
                                    '".addslashes($aime_pas_pourquoi_analyse)."', 
                                    '".addslashes($aimera_analyse)."', 
                                    '".addslashes($aimera_pourquoi_analyse)."', 
                                    0, 
                                    null,
                                    $ideal_monthly_payment,
                                    $bring,
                                    $borrowing_period,
                                    $ideal_budget,
                                    $max_budget,
                                    ".intval(str_replace(' ', '',$capital_total)).",
                                    ".intval(str_replace(' ', '',$epargneTotale))." ,
                                    '$housedPetDog',
                                    '$housedPetCat',
                                    '$housedPetFish'); ";
                    $reqInsert_nov_analyse = $mysqli->query($insert_nov_analyse);
                    $id_Analyse = $mysqli->insert_id;
                                   
                    if ($clientID_bo == '') {
                        array_push($client_not_jointure, "$assetType => $customerID_crm" );     
                        continue;
                          
                    } elseif ($mysqli->error) {
                        error_log("requete => $insert_nov_analyse");
                        error_log("Échec de la requette: %s\n". $mysqli->error);
                        exit();
                        break;
                        
                    }

                    //ajoute les autres investisseurs /OK/
                    foreach ($file_json->investor as $key => $value) {
                        $id_new_client_investor = $clientID_bo;

                        //a partir du deuxieme client inset le client dans la base de donnée (faire une verif si existe déjà)
                        if ( $key != 1 ) {
                            $civilite;
                            if ( $value->gender != '' ) {
                                $civilite = $value->gender == 'male' ? 'M' : 'Mme';
                            }else{
                                $civilite = '';   
                            }

                            // Verifie si le deuxieme investisseur existe déjà snn le créer
                            $select_customer = "SELECT * FROM nov_client_table 
                                                WHERE LOWER(last_name) = LOWER('$value->lastName') AND
                                                    LOWER(first_name) = LOWER('$value->firstName') AND
                                                    LOWER(email) = LOWER('$value->mail') ;";
                            $reqSelect_customer = $mysqli->query($select_customer);
                            if ( $reqSelect_customer != '' ) {
                                $reqSelect_customer = $reqSelect_customer->fetch_assoc();
                            }
                            if ( $reqSelect_customer  != '' ) {
                                $id_new_client_investor = $reqSelect_customer['ID'] ;
                            } else {
                                $tel = $value->tel;
                                $mail = $value->mail;
                                if ($key == 2 && $customerID_crm == 116121 &&  $assetType == 'rp') {
                                    $tel =  $value->mail;
                                    $mail = $value->tel;
                                }
                                
                                $insert_nov_client_table = "INSERT INTO `nov_client_table`(`civilite`, `last_name`, `first_name`, `adresse`, `cp`, 
                                `ville`, `telephone`, `email`, `date_rdv`, `societe`, `statut`, `date_denonciation`, `consultant`, `origine`, 
                                `origine_resa`, `origine_parrain`, `origine_espace_client`, `projet`, `espace_client`) 
                                VALUES ('$civilite','".addslashes($value->lastName)."','".addslashes($value->firstName)."','','','','$tel',
                                '".addslashes($mail)."','','','',null,'','',0,
                                '','','','')";
                                $reqInsert_nov_client_table = $mysqli->query($insert_nov_client_table);
                                $id_new_client_investor = $mysqli->insert_id;
                                if ($mysqli->error) {
                                    error_log("requette $key  $customerID_crm => $insert_nov_client_table");
                                    error_log("Échec de la requette: %s\n". $mysqli->error);
                                    exit();
                                    break;
                                    
                                }
                            }
                        }
                            //insert la jointure client analyse
                        $insert_nov_client_analyse = "INSERT INTO `nov_client_analyse` (id_analyse_pat,
                                                                                    client,
                                                                                    is_deleted)
                                                VALUES ($id_Analyse,
                                                        $id_new_client_investor,
                                                        0);";
                        $reqInsert_nov_client_analyse = $mysqli->query($insert_nov_client_analyse);

                        if ($mysqli->error) {
                            error_log("requette $key  $customerID_crm => $insert_nov_client_analyse");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;  
                        }
                    }

                    ##-- SCRIPT OBJECTIF PATRIMONIAUX --## /NON/ voir position
                    if ( $file_json->goalsCheck != '' ) {


                        $array_order_nov_objectifs_patrimoniaux = explode(',', $file_json->goalsList->order);
                        $numero_champ_nov_objectifs_patrimoniaux = [];

                        if ( $assetType == 'invest' ) {
                            $numero_champ_nov_objectifs_patrimoniaux = [ 'taxReduction' => 1,
                                                                        'immediateAdditionalIncome' => 2,
                                                                        'futureAdditionalIncome' => 3,
                                                                        'investCapital' => 4, 
                                                                        'capitalize' => 5,
                                                                        'familyProtection' => 6,
                                                                        'retirement' => 7,
                                                                        'ifiDrop' => 8,
                                                                        'csgDrop' => 9,
                                                                        'heritage' => 10,
                                                                        'heritageTransmission' => 11, 
                                                                        'saveRegularly' => 12,
                                                                        'payOffLoanEarly' => 13 , 
                                                                        'springboardProject' => 14 ,
                                                                        'capitalGain' => 15 , 
                                                                        'other' => 16 ];
                        } else {
                            $numero_champ_nov_objectifs_patrimoniaux = [ 
                                    'capitalize' => 1,
                                    'familyProtection' => 2,
                                    'retirement' => 3,
                                    'heritage' => 4, 
                                    'heritageTransmission' => 5,
                                    'saveRegularly' => 6,
                                    'stopFullRent' => 7,
                                    'springboardProject' => 8,
                                    'capitalGain' => 9,
                                    'other' => 10,
                            ];

                            //corrige l'odre 
                            if ( COUNT($array_order_nov_objectifs_patrimoniaux) != 10) {
                                $array_order_nov_objectifs_patrimoniaux = ["0" => 1,
                                                                            "1" => 2,
                                                                            "2" => 3,
                                                                            "3" => 4,
                                                                            "4" => 5,
                                                                            "5" => 6,
                                                                            "6" => 7,
                                                                            "7" => 8,
                                                                            "8" => 9,
                                                                            "9" => 10];
                            }

                        }


                        $objectif_check_array = explode(",", $file_json->goalsCheck);

                        foreach ($objectif_check_array as $key => $value) {

                            $position = array_search($numero_champ_nov_objectifs_patrimoniaux[$value], $array_order_nov_objectifs_patrimoniaux) + 1;
                            $label_objectif = $value ;
                            $note_objectif;
                
                            $data = "";
                            $allJson = "";
                            
                            foreach ($file_json->goalsList->$value as $goalKey => $goalValue) {
                                if ($goalKey == "info" ) {
                                    $note_objectif = addslashes($goalValue);
                                }else{
                                    $goalKey_json = '"' . addslashes($goalKey) .'":';
                                    $goalValue_json = '"' . addslashes($goalValue) .'",';
                                    // $goal_json = "{" . $goalKey_json .  $goalValue_json . "}" ;
                                    $goal_json = $goalKey_json .  $goalValue_json ;
                                    $allJson .= $goal_json;
                                } 
                               
                            }
                            $data = "{" . substr($allJson, 0, -1) . "}";

                            if ( $label_objectif != ''  ) {
                                $insert_nov_objectif = "INSERT INTO `nov_objectifs_patrimoniaux` (id_analyse_pat,
                                                                                                objectif,
                                                                                                note,
                                                                                                donnees,
                                                                                                objectif_order)
                                                        VALUES ($id_Analyse,
                                                                '$label_objectif',
                                                                '$note_objectif',
                                                                '$data',
                                                                $position) ;";
                                $reqInsert_nov_objectif = $mysqli->query($insert_nov_objectif);

                                if ($mysqli->error) {
                                    error_log("requette key => $key idAnlayse => $id_Analyse  $customerID_crm => $insert_nov_objectif");
                                    error_log("Échec de la requette: %s\n". $mysqli->error);
                                    exit();
                                    break;
                                    
                                }
                            }
                        }
                        
                    } 
                    // break;

                    ##--  PARCOURS IMMOBILIER --## /OK/
                    if ( $file_json->journey->colleague ) {

                        //verif si le consultant existe déjà /OK/
                        foreach ($file_json->journey->colleague as $key => $value) {

                            $name_consultant = $value->name;

                            $rdv_passe_consultant = bonneDate($value->meetingPastedDate );
                            $rdv_futur_consultant = bonneDate($value->meetingFuturDate );
                            
                            $id_consultant = '';
                
                            $info_nov_confrere = "SELECT id 
                                                    FROM `nov_confrere` 
                                                    WHERE appelation = '$name_consultant' ";
                            $reqInfo_nov_confrere = $mysqli->query($info_nov_confrere);
                            if ($mysqli->query($info_nov_confrere) != '') {
                                $reqInfo_nov_confrere = $reqInfo_nov_confrere->fetch_assoc();
                            }
                            $id_consultant = $reqInfo_nov_confrere['id'];
                            if ( $id_consultant == '' ) {
                
                                $insert_nov_confrere = "INSERT INTO `nov_confrere` (appelation)
                                                        VALUES('".addslashes($name_consultant)."'); ";
                                $reqInsert_nov_confrere = $mysqli->query($insert_nov_confrere);
                                $id_consultant = $mysqli->insert_id;

                                if ($mysqli->error) {
                                    error_log("requette key => $key idAnlayse => $id_Analyse  $customerID_crm => $insert_nov_confrere");
                                    error_log("Échec de la requette: %s\n". $mysqli->error);
                                    exit();
                                    break;
                                    
                                }
                
                            }


                            $insert_nov_confrere_rencontre = "INSERT INTO `nov_confrere_rencontre` (`id_analyse_pat`,
                                                                                                    `confrereID`,
                                                                                                    `rdv_passe`,
                                                                                                    `rdv_futur`)
                                                            VALUES ('$id_Analyse',
                                                                    '$id_consultant',
                                                                    $rdv_passe_consultant,
                                                                    $rdv_futur_consultant );";
                            $mysqli->query($insert_nov_confrere_rencontre);
                            $id_confrereRencontre = $mysqli->insert_id;

                            if ($mysqli->error) {
                                
                                error_log("requette key => $key idAnlayse => $id_Analyse  $customerID_crm => $insert_nov_confrere_rencontre");
                                error_log("Échec de la requette: %s\n". $mysqli->error);
                                exit();
                                break;
                                
                            }
                
                            //ajoute les proposition par consultant sinon ajoute le consultant seul /OK/
                
                            if ( $file_json->proposal ) {
                
                                foreach ($file_json->proposal as $keyProposal => $proposal) {
                                    
                                    if ( $proposal->colleagueName == $name_consultant) {

                                        $insert_rencontre_proposal = " INSERT INTO `nov_confrere_proposal` (`id_analyse_pat`,
                                                                                                                            `id_confrereRencontre`, 
                                                                                                                            `localisation`, 
                                                                                                                            `typologie`, 
                                                                                                                            `prix`, 
                                                                                                                            `logement`, 
                                                                                                                            `fiscalite`, 
                                                                                                                            `promoteur`, 
                                                                                                                            `raison_refus`, 
                                                                                                                            `raison_engagement`, 
                                                                                                                            `souhait`) 
                                                                        VALUES ('$id_Analyse', 
                                                                        '$id_confrereRencontre', 
                                                                        '".addslashes($proposal->place)."', 
                                                                        '".$proposal->type."',
                                                                        '".intval($proposal->price)."', 
                                                                        '".$proposal->lodging."', 
                                                                        '".addslashes($proposal->taxation)."',
                                                                        '".addslashes($proposal->promoter)."', 
                                                                        '".addslashes($proposal->refusal)."',
                                                                        '".addslashes($proposal->pledge)."', 
                                                                        '".addslashes($proposal->follow)."') ";
                                        $mysqli->query( $insert_rencontre_proposal ) ;

                                        if ($mysqli->error) {
                                
                                            error_log("requette key => $key idAnlayse => $id_Analyse  $customerID_crm => $insert_rencontre_proposal");
                                            error_log("Échec de la requette: %s\n". $mysqli->error);
                                            exit();
                                            break;
                                            
                                        }
                                                                                                
                                    }
                                }
                
                            }
                
                        }

                    }

                    ## VOTRE LOGEMENT ACTUEL /OK/
                    $array_order_nov_client_logement = explode(',', $file_json->currentResidence->order);
                    $numero_champ_nov_client_logement = [ 'status' => 1,
                                    'type' => 2,
                                    'typology' => 3,
                                    'housingType' => 4,
                                    'environment' => 5,
                                    'floor' => 6,
                                    'place' => 7,
                                    'area' => 8,
                                    'terrace' => 9,
                                    'livingRoomArea' => 10,
                                    'residenceSize' => 11, 
                                    'yearOfConstruction' => 12,
                                    'annex' => 13 ,
                                    'dateBegin' => 14 ,
                                    'amountOfCharges' => 15 ,
                                    'propertyTax' => 16 ,
                                    'engine' => 17 ,
                                    'electric' => 18 ,
                                    'gaz' => 19 ,
                                    'exposure' => 20 ,
                                    'parking' => 21 ];

                    foreach ($file_json->currentResidence as $key => $value) {
                        $position = array_search($numero_champ_nov_client_logement[$key], $array_order_nov_client_logement) + 1;
                        

                        if ($key != "order" && $value != "" ) {
                            $label_parcour_immo = $key;
                            $value_parcour_immo = $value;

                            //certaines value change de type dans des scripte et devinne des object ?!!
                            if ( gettype($value_parcour_immo) == 'object') {
                                $value_parcour_immo = (array)$value_parcour_immo;
                            }
                            if (gettype($value_parcour_immo) == 'array' ) {
                                $value_parcour_immo = implode('|',$value_parcour_immo);
                                
                            } else {
                                $value_parcour_immo = addslashes($value_parcour_immo);
                               
                            }
                            $insert_parcours_immobilier = "INSERT INTO `nov_client_logement` (`id_analyse_pat`,
                                                                                            `label`,
                                                                                            `valeur`, 
                                                                                            `position`)
                                                        VALUES ($id_Analyse,
                                                                '$label_parcour_immo', 
                                                                '$value_parcour_immo', 
                                                                $position) ";
                            $reqInsert_parcours_immobilier = $mysqli->query($insert_parcours_immobilier);

                            if ($mysqli->error) {
                                
                                error_log("requette key => $key idAnlayse => $id_Analyse  $customerID_crm => $insert_parcours_immobilier");
                                error_log("Échec de la requette: %s\n". $mysqli->error);
                                exit();
                                break;
                                
                            }
                           
                        }
                    }

                    ## NOUVEAU PROJET
                    //adulte et enfant si analyse type rp
                    if ( $assetType == 'rp' ) {

                        foreach ($file_json->newProject->adult as $keyAdult => $valueAdult) {
                            $if_not_empty = false;
                
                            foreach ($valueAdult as $key => $value) {
                                if ( $value != '' ) {
                                    $if_not_empty = true;
                                }
                            }
                            
                            if ( $if_not_empty ) {
                
                                $insert_nov_occupant_logement = "INSERT INTO `nov_occupant_logement` (id_analyse_pat,
                                                                                                        lieu_travail,
                                                                                                        tmps_parcours,
                                                                                                        tmps_souhaite,
                                                                                                        moyen_transport,
                                                                                                        cout_actuel,
                                                                                                        cout_souhaite)
                                                        VALUES($id_Analyse,
                                                                '".addslashes($valueAdult->workplace)."',
                                                                ".intval(str_replace(' ', '',$valueAdult->currentTravelTime)).",
                                                                ".intval(str_replace(' ', '',$valueAdult->desiredTravelTime)).",
                                                                '".addslashes($valueAdult->transportation)."',
                                                                ".intval(str_replace(' ', '',$valueAdult->currentTripCost)).",
                                                                ".intval(str_replace(' ', '',$valueAdult->desiredTripCost)).");";
                                $reqInsert_nov_occupant_logement = $mysqli->query($insert_nov_occupant_logement);
                                if ($mysqli->error) {
                                    error_log("requette $key  $customerID_crm => $insert_nov_occupant_logement");
                                    error_log("Échec de la requette: %s\n". $mysqli->error);
                                    exit();
                                    break;
                                    
                                }
                            }
                        }
                
                        //enfants
                        foreach ($file_json->newProject->children as $keyChild => $valueChild) {
                
                            $if_not_empty = false;
                            foreach ($valueChild as $key => $value) {
                                if ( $value != '' ) {
                                    $if_not_empty = true;
                                }
                            }
                            $age_child = bonneDate($valueChild->age) ;

                
                            if ( $if_not_empty ) {
                
                                $insert_nov_client_enfant = "INSERT INTO `nov_client_enfant` (id_analyse_pat,
                                                                                                        prenom,
                                                                                                        birthdate,
                                                                                                        scolarite,
                                                                                                        localisation,
                                                                                                        extra_scolaire)
                                                        VALUES($id_Analyse,
                                                                '".addslashes($valueChild->firstname)."',
                                                                $age_child,
                                                                '".addslashes($valueChild->school)."',
                                                                '".addslashes($valueChild->location)."',
                                                                '".addslashes($valueChild->extra)."');";
                                $reqInsert_nov_client_enfant = $mysqli->query($insert_nov_client_enfant);
                                if ($mysqli->error) {
                                    error_log("requette $key  $customerID_crm => $insert_nov_client_enfant");
                                    error_log("Échec de la requette: %s\n". $mysqli->error);
                                    exit();
                                    break;
                                    
                                }
                            }
                
                            
                        }
                    }

                    ## NOUVEAU PROJET ## /OK/

                    #localisation, ville ,quartier
                    if ($file_json->cities && $file_json->cities->list != '' ) {
                        $array_cities =explode(',',$file_json->cities->list);
                        $position_citie = 1 ;
                        $order_position_citie = (array) $file_json->cities->order;
                        
                        foreach ($array_cities as $key => $value ) {
                            $zipcode = $value;   
    
                            if ( $file_json->cities->order ) {
                                foreach ($order_position_citie as $keyOrder => $valueOrder) {
                                    if (in_array($valueOrder ,$array_cities) ) {
                                        $zipcode = $valueOrder;
                                        unset($order_position_citie[$keyOrder]);
                                        // break;
                                    }
                                }
                            }
    
                            $localisation = $file_json->postcode->$value;
                            $insert_nov_localisation = "INSERT INTO `nov_localisation` (id_analyse_pat,
                                                                                        localisation,
                                                                                        statut,
                                                                                        position,
                                                                                        zipcode)
                                                        VALUES ($id_Analyse,
                                                                '".addslashes($localisation)."',
                                                                'souhaite',
                                                                $position_citie,
                                                                $zipcode);";
                            $reqInsert_nov_localisation = $mysqli->query($insert_nov_localisation);
                            $position_citie += 1;
                            if ($mysqli->error) {
                                error_log("requette $key  $customerID_crm => $insert_nov_localisation");
                                error_log("Échec de la requette: %s\n". $mysqli->error);
                                exit();
                                break;
                                                
                            }
                        }

                    }


                    #critère et priorités /OK/
                    $count = 0;
                    $note_localisation = true;
                    $array_order_logement_new = explode( ',' ,$file_json->newResidence->order);
                    foreach ($file_json->newResidenceInfo as $key => $value) {

                        $note_logement_new = $file_json->newResidenceInfo->$key;
                        $data = "";
                        $position_logement_new = intval($array_order_logement_new[$count] + 1);


                        foreach (array($file_json->newResidence->$key) as $goalKey => $goalValue) {

                            //certaines value change de type dans des scripte et devinne des object ?!!
                            if ( gettype($goalValue) == 'object') {
                                $goalValue = (array)$goalValue;
                            }

                            
                            if (gettype($goalValue) == 'array') { 
                                $array_test = '[' ;
                                foreach ($goalValue as $k => $v) {
                                    $array_test .= '"' . addslashes($v) .'"' . ',';
                                }
                                trim($array_test);
                                $array_test[strlen($array_test)-1] = ']';
                                $data =  utf8_encode(trim(json_encode($goalValue, JSON_UNESCAPED_SLASHES)));
                                $data =  $array_test;

                            } else {  
                                $data = addslashes($goalValue);
                            } 
                        }

                        if ($data != ']' && $data != '' || $note_logement_new != '') {

                            if ( $note_logement_new != "" || $data !== '[""]' || $data == ']') {
                                if ( $data == ']' ) {
                                    $data = '';
                                }


                                $insert_nov_client_logement_new = "INSERT INTO `nov_client_logement_new` (id_analyse_pat,
                                                                                                        label,
                                                                                                        valeur,
                                                                                                        note,
                                                                                                        position)
                                                                VALUES($id_Analyse,
                                                                        '$key',
                                                                        '$data',
                                                                        '".addslashes(strval($note_logement_new))."',
                                                                        '$position_logement_new');";
                                $reqInsert_nov_client_logement_new = $mysqli->query($insert_nov_client_logement_new);
                                if ($mysqli->error) {
                                    error_log("requette $key  $customerID_crm => $insert_nov_client_logement_new");
                                    error_log("Échec de la requette: %s\n". $mysqli->error);
                                    exit();
                                break;
                                                
                            }
                                
                            }
                        } 
                        $count += 1;
                    }


                    //Select tous les client de l'analyse pat pour req les id 
                    $select_client_analyse = "SELECT * FROM nov_client_analyse WHERE id_analyse_pat = $id_Analyse ";
                    $lastData = $mysqli->query($select_client_analyse);

                    $reqSelect_client_analyse = [];
                    while ($row = $lastData->fetch_assoc()) {
                        $reqSelect_client_analyse[] = $row;
                    }

                    ## ETAT CIVIL ## /OK/
                    foreach ($file_json->civilStatus as $key => $value) {  
                    
                        $id_client = $reqSelect_client_analyse[$key - 1]['client'] ;
                        $birthdate = bonneDate($value->birthdate);
                        // error_log("Format de base => ". $value->birthdate);
                        // error_log("Voici un bon format => ". bonneDate($value->birthdate));
                        $regime_patrimonial = $value->matrimonialeRegime;
                        $enfant = $value->childrenNumber;
                        $age = $value->age;
                        $etude = $value->school;
                        $part_fiscale = $value->taxSharesNumber;
                        $decisionnaire = $value->projectDecisionMaker;

                        $enfant_escaped = mysqli_real_escape_string($mysqli, $enfant);
                        $etude_escaped = mysqli_real_escape_string($mysqli, $etude);
                        $decisionnaire_escaped = mysqli_real_escape_string($mysqli, $decisionnaire);
                        
                        $insert_nov_etat_civil = "INSERT INTO `nov_etat_civil` (clientID ,
                                                                                birthdate,
                                                                                regime_patrimonial,
                                                                                enfant,
                                                                                age,
                                                                                etude,
                                                                                part_fiscale,
                                                                                decisionnaire,
                                                                                nationalite,
                                                                                titre_sejour,
                                                                                titre_duree,
                                                                                titre_expiration,
                                                                                charge_gestion,
                                                                                regime_contrat,
                                                                                garde_alternee,
                                                                                garde_enfant)
                                                    VALUES ($id_client,
                                                            $birthdate,
                                                            '".addslashes($regime_patrimonial)."',
                                                            '".addslashes($enfant_escaped)."',
                                                            '".addslashes($age)."',
                                                            '".addslashes($etude_escaped)."',
                                                            '".addslashes($part_fiscale)."',
                                                            '".addslashes($decisionnaire_escaped)."','','','', null ,'','','','')";
                        $reqinsert_nov_etat_civil= $mysqli->query($insert_nov_etat_civil);
                        if ($mysqli->error) {
                            error_log("requette $key  $customerID_crm => $insert_nov_etat_civil");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;
                        }


                    }

                    ## REVENUS PROFESSIONNELS ## /OK/
                    foreach ($file_json->professionalIncome as $key => $value) {

                        $id_client = $reqSelect_client_analyse[$key - 1]['client'] ;
                        $seniority =  bonneDate($value->seniority);
                        $realCost = $value->incomeTax == 'real' ? intval(str_replace(' ', '',$value->realCosts)) : 0 ;
                        $insert_nov_revenue_pro = "INSERT INTO `nov_revenu_pro` (clientID,
                                                                                profession,
                                                                                entreprise,
                                                                                anciennete,
                                                                                contrat,
                                                                                salaire,
                                                                                prime,
                                                                                interessement,
                                                                                impot_revenu,
                                                                                frais_reel,
                                                                                dividende,
                                                                                bic,
                                                                                bnc,
                                                                                ba,
                                                                                pension,
                                                                                autre_revenu,
                                                                                impot,
                                                                                reduc_impot,
                                                                                rfrn1,
                                                                                rfrn2)
                                        VALUES($id_client,
                                                '".addslashes($value->profession)."',
                                                '".addslashes($value->company)."',
                                                $seniority,
                                                '".addslashes($value->contractType)."',
                                                '".addslashes($value->pay)."',
                                                '".addslashes($value->bonus)."',
                                                '".addslashes($value->profitSharing)."',
                                                '".addslashes($value->incomeTax)."',
                                                ".intval(str_replace(' ', '',$realCost)).",
                                                ".intval(str_replace(' ', '',$value->dividends)).",
                                                ".intval(str_replace(' ', '',$value->bic)).",
                                                ".intval(str_replace(' ', '',$value->bnc)).",
                                                ".intval(str_replace(' ', '',$value->ba)).",
                                                ".intval(str_replace(' ', '',$value->alimony)).",
                                                ".intval(str_replace(' ', '',$value->otherIncome)).",
                                                ".intval(str_replace(' ', '',$value->taxes)).",
                                                ".intval(str_replace(' ', '',$value->taxReduction)).",
                                                ".intval(str_replace(' ', '', $value->rfrn1)).",
                                                ".intval(str_replace(' ', '',$value->rfrn2)).");";
                        $reqInsert_nov_revenue_pro = $mysqli->query($insert_nov_revenue_pro);
                        if ($mysqli->error) {
                            error_log("requette $key  $customerID_crm => $insert_nov_revenue_pro");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;
                        }
            
                    }
            
                    ##REVENU LOCATIF /OK/
                    foreach ($file_json->rentalIncome as $key => $value) {
                        
                        $acquisitionDate =  bonneDate($value->acquisitionDate);

                        $investisseur = [];
                        if ( $value->owner ) {
                            foreach ($value->owner as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    array_push($investisseur , $valueInvestor);
                                    break;
                                } 
                                array_push($investisseur, $valueInvestor);
                            }
                        }
                        $investisseur = implode(',',$investisseur);
                        
                        $insert_nov_revenu_locatif = "INSERT INTO `nov_revenu_locatif` (id_analyse_pat,
                                                                                        investisseurs,
                                                                                        vetuste,
                                                                                        localisation,
                                                                                        acquisition_date,
                                                                                        typologie,
                                                                                        loyer,
                                                                                        acquisition_prix,
                                                                                        emprunt,
                                                                                        apport,
                                                                                        capital_du,
                                                                                        duree_emprunt,
                                                                                        taux_emprunt,
                                                                                        charge_gestion,
                                                                                        differe,
                                                                                        charge_copro,
                                                                                        fiscalite)
                                                    VALUES($id_Analyse,
                                                            '$investisseur',
                                                            '$value->type',
                                                            '".addslashes($value->place)."',
                                                            $acquisitionDate,
                                                            '$value->typology',
                                                            '".intval(str_replace(' ', '',$value->rent))."',
                                                            '".intval(str_replace(' ', '',$value->buyingPrice))."',
                                                            '".intval(str_replace(' ', '',$value->amountBorrowed))."',
                                                            '".intval(str_replace(' ', '',$value->personalContribution))."',
                                                            '".intval(str_replace(' ', '',$value->crd))."',
                                                            '".intval(str_replace(' ', '',$value->borrowingPeriod))."',
                                                            '$value->borrowingRate',
                                                            '$value->managementCharge',
                                                            '$value->deferredLoan',
                                                            '".intval(str_replace(' ', '',$value->condominiumFees))."',
                                                            '$value->taxation');";
                        $reqInsert_nov_revenu_locatif = $mysqli->query($insert_nov_revenu_locatif);
                        $newid_revenu_locatif = $mysqli->insert_id;
                        $investisseur = explode(",",$investisseur);

                        if ($mysqli->error) {
                            error_log("requette $key  $customerID_crm => $insert_nov_revenu_locatif");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;
                        }

                        if ( $value->owner ) {
                            //jointure investisseur et id nov revenue locatif /OK/
                            foreach ($investisseur as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    foreach ($reqSelect_client_analyse as $k => $v) {
                                    $insert_nov_locatif_proprio = "INSERT INTO `nov_locatif_proprio` (revenuLocatifID,clientID)
                                        VALUES($newid_revenu_locatif,".$v['client']." );";
                                        $reqInsert_nov_locatif_proprio = $mysqli->query($insert_nov_locatif_proprio);
                                        if ($mysqli->error) {
                                            error_log("requette $key  $customerID_crm => $insert_nov_locatif_proprio");
                                            error_log("Échec de la requette: %s\n". $mysqli->error);
                                            exit();
                                            break;
                                        }
                                    }
                                } else {
                                    $insert_nov_locatif_proprio = "INSERT INTO `nov_locatif_proprio` (revenuLocatifID, clientID)
                                    VALUES($newid_revenu_locatif,".$reqSelect_client_analyse[$valueInvestor - 1]['client']." );";
                                    $reqInsert_nov_locatif_proprio = $mysqli->query($insert_nov_locatif_proprio);
                                    if ($mysqli->error) {
                                        error_log("requette $key  $customerID_crm => $insert_nov_locatif_proprio");
                                        error_log("Échec de la requette: %s\n". $mysqli->error);
                                        exit();
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    ##RESIDENCE PRINCIPAL /OK/
                    foreach ($file_json->principalResidence as $key => $value) {

                        $acquisitionDate = bonneDate($value->acquisitionDate);
                        $investisseur = [];
                        if ( $value->owner ) {
                            foreach ($value->owner as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    array_push($investisseur , $valueInvestor);
                                    break;
                                } 
                                array_push($investisseur, $valueInvestor);
                            }
                        }
                        $investisseur = implode(',',$investisseur);

                        $insert_nov_residence = "INSERT INTO `nov_residence`(id_analyse_pat,
                                                            investisseurs,
                                                            statut,
                                                            localisation,
                                                            acquisition_date,
                                                            typologie,
                                                            loyer,
                                                            acquisition_rpix,
                                                            emprunt,
                                                            apport,
                                                            capital_du,
                                                            duree_emprunt,
                                                            taux_emprunt,
                                                            taxe_fonciere,
                                                            charge_copro,
                                                            type)
                                        VALUES($id_Analyse,
                                                '$investisseur',
                                                '$value->status',
                                                '".addslashes($value->place)."',
                                                $acquisitionDate,
                                                '$value->typology',
                                                '".intval(str_replace(' ', '',$value->rent))."',
                                                '".intval(str_replace(' ', '',$value->buyingPrice))."',
                                                '".intval(str_replace(' ', '',$value->amountBorrowed))."',
                                                '".intval(str_replace(' ', '',$value->personalContribution))."',
                                                '".intval(str_replace(' ', '',$value->crd))."',
                                                '".intval(str_replace(' ', '',$value->borrowingPeriod))."',
                                                ".intval(str_replace(' ', '',$value->borrowingRate)).",
                                                '$value->propertyTax',
                                                ".intval(str_replace(' ', '',$value->condominiumFees)).",
                                                'résidence principale');";
                        $reqInsert_nov_residence = $mysqli->query($insert_nov_residence);
                        if ($mysqli->error) {
                            error_log("requette $key  $customerID_crm => $insert_nov_residence");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;
                        }
                        $newid_residence_principal = $mysqli->insert_id;
                        $investisseur = explode(",",$investisseur);

                        if ( $value->owner ) {
                            //jointure investisseur et id nov revenue locatif /OK/
                            foreach ($investisseur as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    foreach ($reqSelect_client_analyse as $k => $v) {
                                        $insert_nov_residence_principal = "INSERT INTO `nov_residence_client` (residenceID,clientID)
                                        VALUES($newid_residence_principal,".$v['client']." );";
                                        $reqInsert_nov_residence_principal = $mysqli->query($insert_nov_residence_principal);
                                        if ($mysqli->error) {
                                            error_log("requette $key  $customerID_crm => $insert_nov_residence_principal");
                                            error_log("Échec de la requette: %s\n". $mysqli->error);
                                            exit();
                                            break;
                                        }
                                    }
                                } else {
                                    $insert_nov_residence_principal = "INSERT INTO `nov_residence_client` (residenceID, clientID)
                                    VALUES($newid_residence_principal,".$reqSelect_client_analyse[$valueInvestor - 1]['client']." );";
                                    $reqInsert_nov_residence_principal = $mysqli->query($insert_nov_residence_principal);
                                    if ($mysqli->error) {
                                        error_log("requette $key  $customerID_crm => $insert_nov_residence_principal");
                                        error_log("Échec de la requette: %s\n". $mysqli->error);
                                        exit();
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    ##RESIDENCE SECONDAIRE /OK/
                    foreach ($file_json->secondResidence as $key => $value) {

                        $acquisitionDate = bonneDate($value->acquisitionDate);
                        $investisseur = [];
                        if ( $value->owner ) {
                            foreach ($value->owner as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    array_push($investisseur , $valueInvestor);
                                    break;
                                } 
                                array_push($investisseur, $valueInvestor);
                            }
                        }
                        $investisseur = implode(',',$investisseur);

                        $insert_nov_residence = "INSERT INTO `nov_residence`(id_analyse_pat,
                                                            investisseurs,
                                                            statut,
                                                            localisation,
                                                            acquisition_date,
                                                            typologie,
                                                            loyer,
                                                            acquisition_rpix,
                                                            emprunt,
                                                            apport,
                                                            capital_du,
                                                            duree_emprunt,
                                                            taux_emprunt,
                                                            taxe_fonciere,
                                                            charge_gestion,
                                                            charge_copro,
                                                            type)
                                        VALUES($id_Analyse,
                                                '$investisseur',
                                                '$value->status',
                                                '".addslashes($value->place)."',
                                                $acquisitionDate,
                                                '$value->typology',
                                                '".intval(str_replace(' ', '',$value->rent))."',
                                                '".intval(str_replace(' ', '',$value->buyingPrice))."',
                                                '".intval(str_replace(' ', '',$value->amountBorrowed))."',
                                                '".intval(str_replace(' ', '',$value->personalContribution))."',
                                                '".intval(str_replace(' ', '',$value->crd))."',
                                                '".intval(str_replace(' ', '',$value->borrowingPeriod))."',
                                                ".intval(str_replace(' ', '',$value->borrowingRate)).",
                                                '$value->propertyTax',
                                                '$value->managementCharge',
                                                ".intval(str_replace(' ', '',$value->condominiumFees)).",
                                                'residence secondaire');";
                        $reqInsert_nov_residence = $mysqli->query($insert_nov_residence);
                        if ($mysqli->error) {
                            error_log("requette $key  $customerID_crm => $insert_nov_residence");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;
                        }
                        $newid_residence_secondaire = $mysqli->insert_id;
                        $investisseur = explode(",",$investisseur);

                        if ( $value->owner ) {
                            //jointure investisseur et id nov revenue locatif /OK/
                            foreach ($investisseur as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    foreach ($reqSelect_client_analyse as $k => $v) {
                                        $insert_nov_residence_second = "INSERT INTO `nov_residence_client` (residenceID,clientID)
                                        VALUES($newid_residence_secondaire,".$v['client']." );";
                                        $reqInsert_nov_residence_second = $mysqli->query($insert_nov_residence_second);
                                        if ($mysqli->error) {
                                            error_log("requette $key  $customerID_crm => $insert_nov_residence_second");
                                            error_log("Échec de la requette: %s\n". $mysqli->error);
                                            exit();
                                            break;
                                        }
                                    }
                                } else {
                                    $insert_nov_residence_second = "INSERT INTO `nov_residence_client` (residenceID, clientID)
                                    VALUES($newid_residence_secondaire,".$reqSelect_client_analyse[$valueInvestor - 1]['client']." );";
                                    $reqInsert_nov_residence_second = $mysqli->query($insert_nov_residence_second);
                                    if ($mysqli->error) {
                                        error_log("requette $key  $customerID_crm => $insert_nov_residence_second");
                                        error_log("Échec de la requette: %s\n". $mysqli->error);
                                        exit();
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    ##CHARGE /OK/
                    foreach ($file_json->charge as $key => $value) {
                        $souscription_date = bonneDate($value->date);

                        $investisseur = [];
                        if ( $value->owner ) {
                            foreach ($value->owner as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    array_push($investisseur , $valueInvestor);
                                    break;
                                } 
                                array_push($investisseur, $valueInvestor);
                            }
                        }
                        $investisseur = implode(',',$investisseur);


                        $autre_type =  '';
                        if ($value->type == 'autre_prêt' || $value->type == 'autre_charge' ) {
                            $autre_type = $value->other ? $value->other : '';
                        } 

                        $insert_charge = "INSERT INTO `nov_charge` (id_analyse_pat,
                                                                    investisseurs,
                                                                    type,
                                                                    autre_type,
                                                                    souscription_date,
                                                                    mensualite,
                                                                    duree,
                                                                    taux,
                                                                    capital_du)
                                        VALUES ($id_Analyse,
                                                '$investisseur',
                                                '$value->type',
                                                '".addslashes($autre_type)."',
                                                $souscription_date,
                                                ".intval(str_replace(' ', '',$value->month)).",
                                                ".intval(str_replace(' ', '',$value->duration)).",
                                                ".intval(str_replace(' ', '',$value->taux)).",
                                                ".intval(str_replace(' ', '',$value->amount)).");";
                        $reqInsert_charge = $mysqli->query($insert_charge);
                        if ($mysqli->error) {
                            error_log("requette $key  $customerID_crm => $insert_charge");
                            error_log("Échec de la requette: %s\n". $mysqli->error);
                            exit();
                            break;
                        }
                        $newid_charge = $mysqli->insert_id;
                        $investisseur = explode(",",$investisseur);

                        if ( $value->owner ) {
                            //jointure investisseur et id nov revenue locatif /OK/
                            foreach ($investisseur as $keyInvestor => $valueInvestor) {
                                if ( $valueInvestor == 'all' ) {
                                    foreach ($reqSelect_client_analyse as $k => $v) {
                                        $insert_nov_charge_client = "INSERT INTO `nov_charge_client` (chargeID,clientID)
                                        VALUES($newid_charge,".$v['client']." );";
                                        $reqInsert_nov_charge_client = $mysqli->query($insert_nov_charge_client);
                                        if ($mysqli->error) {
                                            error_log("requette $key  $customerID_crm => $insert_nov_charge_client");
                                            error_log("Échec de la requette: %s\n". $mysqli->error);
                                            exit();
                                            break;
                                        }
                                    }
                                } else {
                                        $insert_nov_charge_client = "INSERT INTO `nov_charge_client` (chargeID, clientID)
                                        VALUES($newid_charge,".$reqSelect_client_analyse[$valueInvestor - 1]['client']." );";
                                        $reqInsert_nov_charge_client = $mysqli->query($insert_nov_charge_client);
                                        if ($mysqli->error) {
                                            error_log("requette $key  $customerID_crm => $insert_nov_charge_client");
                                            error_log("Échec de la requette: %s\n". $mysqli->error);
                                            exit();
                                            break;
                                        }
                                }
                            }
                        }
                    }

                    ##EPARGNE DISPONIBLE POUR VOTRE PROJET  /OK/
                    //banque /OK/
                    foreach ($file_json->bank as $keyBank => $valueBank) {
                        $if_bank_common = $keyBank == 'common' ? 1 : 0;
                        foreach ($valueBank as $keyStatus => $valueStatus) {

                            if ($valueStatus != '') {

                                $select_bank_liste = "SELECT * FROM nov_bank_liste WHERE nom = '".addslashes($valueStatus)."' ";
                                $reqSelect_bank_liste = $mysqli->query($select_bank_liste);
                                if ( $reqSelect_bank_liste != '' ) {
                                    $reqSelect_bank_liste = $reqSelect_bank_liste->fetch_assoc();      
                                }         
                                
                                $id_bank = '';

                                //verifie si la banque existe ? snn insert et recupe l'id
                                if ($reqSelect_bank_liste['ID'] == '' ) {
                                    $insert_nov_bank_liste = "INSERT INTO `nov_bank_liste` (nom)
                                                                VALUES('".addslashes($valueStatus)."')";
                                    $reqInsert_nov_bank_liste = $mysqli->query($insert_nov_bank_liste);
                                    if ($mysqli->error) {
                                        error_log("requette $key  $customerID_crm => $insert_nov_bank_liste");
                                        error_log("Échec de la requette: %s\n". $mysqli->error);
                                        exit();
                                        break;
                                    }     
                                    $id_bank = $mysqli->insert_id;

                                } else {
                                    $id_bank = $reqSelect_bank_liste['ID'];
                                }

                                //si la banque est common insert pour tous les investisseur
                                if ( $keyBank == 'common' ) {
                                    foreach ($reqSelect_client_analyse as $k => $v) {
                                        $insert_nov_banque_client = "INSERT INTO `nov_banque_client` (id_analyse_pat,
                                                                                                banqueID,
                                                                                                clientID,
                                                                                                statut,
                                                                                                nom,
                                                                                                banque_commun)
                                                                VALUES ($id_Analyse,
                                                                        '$id_bank',
                                                                        '".$v['client']."',
                                                                        '$keyStatus',
                                                                        '".addslashes($valueStatus)."',
                                                                        '$if_bank_common')";
                                        $reqInsert_nov_banque_client = $mysqli->query($insert_nov_banque_client);    
                                        if ($mysqli->error) {
                                            error_log("requette $key  $customerID_crm => $insert_nov_banque_client");
                                            error_log("Échec de la requette: %s\n". $mysqli->error);
                                            exit();
                                            break;
                                        }             
                                    }
                                } else  {

                                    $insert_nov_banque_client = "INSERT INTO `nov_banque_client` (id_analyse_pat,
                                                                                                banqueID,
                                                                                                clientID,
                                                                                                statut,
                                                                                                nom,
                                                                                                banque_commun)
                                                                VALUES ($id_Analyse,
                                                                        '$id_bank',
                                                                        '".$reqSelect_client_analyse[$keyBank - 1]['client']."',
                                                                        '$keyStatus',
                                                                        '".addslashes($valueStatus)."',
                                                                        '$if_bank_common')";
                                    $reqInsert_nov_banque_client = $mysqli->query($insert_nov_banque_client); 
                                    if ($mysqli->error) {
                                        error_log('je suis ici');
                                        error_log("requette $key  $customerID_crm => $insert_nov_banque_client");
                                        error_log("Échec de la requette: %s\n". $mysqli->error);
                                        exit();
                                        break;
                                    }     
                                    
                                
                                }
                                
                            }

                        }
                        
                    }

                    //epargne /OK/
                    foreach ($file_json->switch as $keySwitch => $valueSwitch) {
                        if ( $valueSwitch == 1 ) {
                            preg_match('/group_epargne_(.*)/', $keySwitch , $matches);
                            $key_epargne = $matches[1];
                            if (isset($file_json->epargne->$key_epargne)) {
                                foreach ($file_json->epargne->$key_epargne as $key_satus_epargne => $valueEpargne) {
                                    $if_epargne_common = $key_satus_epargne == 'common' ? 1 : 0;
                                    $what_client = $key_satus_epargne == 'common' ?
                                        $reqSelect_client_analyse[0]['client'] :
                                        $reqSelect_client_analyse[$key_satus_epargne - 1]['client'];

                                    if ($valueEpargne->capital != '' || $valueEpargne->epargne != '' || $valueEpargne->renta != '') {
                                        $insert_epargne = " INSERT INTO `nov_banque` (`id_analyse_pat`,
                                                                                `dispositif`,
                                                                                `capital`,
                                                                                `epargne`,
                                                                                `rentabilte`,
                                                                                `banque_commun`,
                                                                                `clientID`) 
                                        VALUES ('$id_Analyse',
                                                '$key_epargne',
                                                " . intval(str_replace(' ', '', $valueEpargne->capital)) . ",
                                                " . intval(str_replace(' ', '', $valueEpargne->epargne)) . ",
                                                " . intval(str_replace(' ', '', $valueEpargne->renta)) . ",
                                                " . intval($if_epargne_common) . ",
                                                '$what_client')";
                                        $reqInsert_epargne = $mysqli->query($insert_epargne);
                                        if ($mysqli->error) {
                                            error_log("requette $key  $customerID_crm => $insert_epargne");
                                            error_log("Échec de la requette: %s\n" . $mysqli->error);
                                            exit();
                                            break;
                                        }
                                    }
                                }
                            }

                        }
                    }

                    ##NOTE /OK/
                    foreach ($file_json->note as $keyPage => $valuePage) {
                        if ($valuePage != '') {
                            $page_number = $keyPage + 1;
                            $insert_nov_analyse_patrimoniale_note = "INSERT INTO `nov_analyse_patrimoniale_note` (id_analyse_pat,
                                                                                                            page_nb,
                                                                                                            note)
                                VALUES($id_Analyse,
                                        $page_number,
                                        '".addslashes($valuePage)."');";
                            $reqInsert_nov_analyse_patrimoniale_note = $mysqli->query($insert_nov_analyse_patrimoniale_note);
                            if ($mysqli->error) {
                                error_log("requette $key  $customerID_crm => $reqInsert_nov_analyse_patrimoniale_note");
                                error_log("Échec de la requette: %s\n". $mysqli->error);
                                exit();
                                break;
                            }     
                        }
                    }
                        
                    // break;
                } 
            }
          
        }
        error_log(print_r($client_not_jointure,true));
        error_log('fin de transfert => donnée analyse pat');
    }


}
