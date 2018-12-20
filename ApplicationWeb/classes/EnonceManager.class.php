<?php
class EnonceManager {

	//Conctructeur
	public function __construct($db){
		$this->db = $db;
	}

	//Fonction permettant de créer un objet Enonce à partir d'un tableau
	public function createEnonceDepuisTableau($paramsEnonce){
		return new Enonce($paramsEnonce);
	}


	public function ajouterEnonce($newEnonce){
		if(!empty($newEnonce)){

      $req = $this->db->prepare(
        "INSERT INTO `enonce`(`enonce`) VALUES (:enonce)"
      );

      $req->bindValue(':enonce',$newEnonce->getEnonce(),PDO::PARAM_STR);

      $result = $req->execute();

			$req->closeCursor();

      return $result;

    }
	}

}

?>