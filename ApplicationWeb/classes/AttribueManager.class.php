<?php

class AttribueManager {
    private $db;

    /**
     * Retourne une nouvelle instance d'AttribueManager.
     * @param MyPDO $db Une instance de MyPDO.
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Stocke un Attribue dans la base de données.
     * @param Attribue $Attribue L'instance d'Attribue à enregistrer.
     */
    public function addAttribue($Attribue)
    {
        $req = $this->db->prepare(
					'INSERT INTO attribue(idUtilisateur,idSujet,dateAttribution,dateLimite)
		VALUES (:idUtilisateur,:idSujet,:dateAttribution,:dateLimite)');
        $req->bindValue(':idUtilisateur', $Attribue->getIdUtilisateur(), PDO::PARAM_STR);
        $req->bindValue(':idSujet', $Attribue->getIdSujet(), PDO::PARAM_STR);
        $req->bindValue(':dateAttribution', $Attribue->getDateAttribution(), PDO::PARAM_STR);
        $req->bindValue(':dateLimite', $Attribue->getIdUtilisateur(), PDO::PARAM_STR);
        $req->execute();
    }

    /**
     * Retourne une instance d'Attribue à partir de son idUtilisateur et son idSujet.
     * @param integer $idUtilisateur L'id de l'Utilisateur de l'Attribue à récupérer.
     * @param integer $idSujet L'id du Sujet de l'Attribue à récupérer.
     * @return Attribue Une instance d'Attribue correspondant aux paramètres spécifiés.
     */
    public function getAttribueById($idUtilisateur, $idSujet)
    {
        $req = $this->db->prepare(
            'SELECT idUtilisateur,idSujet,dateAttribution, dateLimite
						FROM attribue WHERE idUtilisateur= :idUtilisateur && idSujet= :idSujet'
        );
        $req->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);
        $req->bindValue(':idSujet', $idSujet, PDO::PARAM_STR);
        $req->execute();
        $attribue = $req->fetch(PDO::FETCH_OBJ);
        $req->closeCursor();
        return new Attribue($attribue);
    }

    /**
     * Supprime toutes les instances de Attribue liées à l''Utilisateur ayant l'id spécifié.
     * @param integer $id L'ID représentant l'Utilisateur dont on veut supprimer les instances d'Attribue.
     */
    public function supprimerAttribueAvecIdEtudiant($id) {
        $req = $this->db->prepare("DELETE FROM attribue WHERE idUtilisateur = :id");
        $req->bindValue(':id', $id, PDO::PARAM_STR);
        $req->execute();
        $req->closeCursor();
    }

    /**
     * Retourne un tableau avec la liste de tous les Utilisateurs n'ayant pas répondu au Sujet spécifié.
     * @param integer $idSujet L'ID du Sujet dont on veut récupérer les élèves n'ayant pas répondu.
     * @return array Un tableau avec toutes les instances d'Utilisateur n'ayant pas répondu au sujet.
     */
    public function getListeElevesNAyantPasRepondu($idSujet) {
        $req = $this->db->prepare('
            SELECT idUtilisateur FROM utilisateur WHERE estProf = 0 AND idUtilisateur NOT IN (
                SELECT idUtilisateur FROM reponses WHERE idSujet = :idSujet
                HAVING COUNT(idUtilisateur) > 0
            ) AND idUtilisateur IN (
                SELECT idUtilisateur FROM attribue WHERE idSujet = :idSujet
            )
        ');
        $req->bindValue(':idSujet', $idSujet, PDO::PARAM_STR);
        $req->execute();
        $listeEleves = array();
        while($eleve = $req->fetch(PDO::FETCH_OBJ)) {
            $listeEleves[] = $eleve;
        }
        $req->closeCursor();
        return $listeEleves;
    }

		public function getIdSujetMaximumByIdEnonce($idEnonce){
				$req = $this->db->prepare('
						SELECT MAX(idSujet) as idSujetMax FROM sujet WHERE idEnonce = :idEnonce

				');
				$req->bindValue(':idEnonce', $idEnonce, PDO::PARAM_INT);
				$req->execute();
				$idSujet = $req->fetch(PDO::FETCH_OBJ);
        $idSujetMax = $idSujet->idSujetMax;
        $req->closeCursor();
				return $idSujetMax;
		}
}
