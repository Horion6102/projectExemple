<?php
require_once "../php/database/database.php";
require_once "../php/library/utils.php";
class Site{
    private $Siret;
    private $CodePostal;
    private $Rue;
    private $Pays;
    private $MailSite;

    /**
     * @param $Siret
     * @param $Pays
     * @param $MailSite
     */
    public function __construct($Siret, $Pays, $MailSite)
    {
        $this->Siret = $Siret;
        $this->CodePostal = getCodePostalEntreprise($Siret);
        $this->Rue = getAdresseEntreprise($Siret);
        $this->Pays = $Pays;
        $this->MailSite = $MailSite;
    }

    /**
     * @return mixed
     */
    public function getSiret()
    {
        return $this->Siret;
    }

    /**
     * @return Exception|mixed
     */
    public function getCodePostal()
    {
        return $this->CodePostal;
    }

    /**
     * @return Exception|string
     */
    public function getRue()
    {
        return $this->Rue;
    }

    /**
     * @return mixed
     */
    public function getPays()
    {
        return $this->Pays;
    }

    /**
     * @return mixed
     */
    public function getMailSite()
    {
        return $this->MailSite;
    }


    public static function getSiteParId($id)
    {
        $bdd = Database::getDB();
        $requeteListeSite = $bdd->query("select * from SITE where SIT_IDENTIFIANT = $id");
        $resulat = $requeteListeSite->fetchAll();
        var_dump($resulat);
        $Site = new Site($resulat[0]["SIT_SIREN"], $resulat[0]["SIT_PAYS"],  $resulat[0]["SIT_MAIL"]);
        return $Site;
    }

    public static function getSiteParSiret($Siret)
    {
        $bdd = Database::getDB();
        $requeteListeSite = $bdd->query("select * from SITE where SIT_SIREN = $Siret");
        $resulat = $requeteListeSite->fetchAll();
        $Site = new Site($resulat[0]["SIT_SIREN"], $resulat[0]["SIT_PAYS"],  $resulat[0]["SIT_MAIL"]);
        return $Site;
    }

    public static function insererSite($SiretSite, $PaysSite, $sirenEntreprise, $mailSite)
    {
        $bdd = Database::getDB();
        $insert = $bdd->prepare('INSERT INTO SITE(SIT_SIREN, SIT_CODE_POSTAL, SIT_RUE, SIT_PAYS, SIT_MAIL) VALUES(:SirenSite, :codePostal, :rue, :pays, :mailSite)');
        $retour = $insert->execute(array(
            'SirenSite' => $SiretSite,
            'codePostal' => getCodePostalEntreprise($SiretSite),
            'rue' => getAdresseEntreprise($SiretSite),
            'pays' => $PaysSite,
            'mailSite' => $mailSite
        ));
        if ($retour == false) echo "une erreur est survenue";


        $insert = $bdd->prepare('INSERT INTO CONTENIR(SIT_SIREN, ENT_SIRET) VALUES(:SirenSite, :siretEntreprise)');
        $retour = $insert->execute(array(
            'SirenSite' => $SiretSite,
            'siretEntreprise' => $sirenEntreprise
        ));
        if ($retour == false) echo "une erreur est survenue";
    }

    public static function getListeSitePourEntreprise($Entreprise){
        $bdd = Database::getDB();
        $Siren = $Entreprise->getSiren();
        $IdentifiantEntreprise = Entreprise::getIdentifiantEntreprise($Siren);
        $requetelisteIdentifiantSite = $bdd->query("select * from CONTENIR where ENT_IDENTIFIANT = $IdentifiantEntreprise");
        $listeSite = array();
        var_dump($requetelisteIdentifiantSite);
        foreach($requetelisteIdentifiantSite->fetchAll() as $a) {
            $Site = Site::getSiteParId($a["SIT_IDENTIFIANT"]);
            array_push($listeSite, $Site);
        }
        return $listeSite;
    }

    public static function supprimerSiteParSiret($siret){
        $bdd = Database::getDB();
        $suppression = $bdd->prepare("DELETE FROM CONTENIR WHERE SIT_SIREN = :siret");
        $retour = $suppression->execute([
            'siret' => $siret,
        ]);
        if ($retour == false) echo "Une erreur est survenue, cela peut être dû au fait qu'un stage est proposé sur ce site.";

        $suppression = $bdd->prepare("DELETE FROM SITE WHERE SIT_SIREN = :siret");
        $retour = $suppression->execute([
            'siret' => $siret,
        ]);
        if ($retour == false) echo "Une erreur est survenue, cela peut être dû au fait qu'un stage est proposé sur ce site.";
    }


}
?>
