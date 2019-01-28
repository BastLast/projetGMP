<?php
if(!isset($_SESSION)){
  session_start();
}
?>

var fontWeight = ["normal" , "bold"];
var fontStyle = ["normal" , "italic"];
var textDecoration = ["none" , "underline"];
var fontSize = ["60%", "80%", "100%", "150%", "200%", "300%", "400%", "500%"];

var isBoldSelected = false;
var isItalicSelected = false;
var isUnderlineSelected = false;
var policeSize = 3;

var itemASuppr = [];
var numItem = 0;
var numQR = recupererNumQuestionReponseAjax();
var contientQuestion = false;
var contientDonneeVariable = false;
var contientText = false;
var creationEnonceReady = false;

var tableauQuestions = new Array(); //Création du tableau mémorisant les questions de l'énoncé
var tableauNumParams = new Array(); //Création du tableau mémorisant les id des paramètres de chaque questions

//Attendre que le document soit compvarement chargé
$(document).ready(function() {

  //Récupérer les éléments de l'ihm nécessaire
  var blockParametrageText = document.getElementById("blockParametrageText");
  var blockParametrageDonneeVariable = document.getElementById("blockParametrageDonneeVariable");
  var blockParametrageDonneeCalculee = document.getElementById("blockParametrageDonneeCalculee");
  var blockParametrageImage = document.getElementById("blockParametrageImage");
  var boutonAjouterDonneeVariable = document.getElementById("boutonAjouterDonneeVariable");
  var boutonAjouter = document.getElementById("boutonAjouter");
  var boutonSupprimer = document.getElementById("boutonSupprimer");
  var boldButton = document.getElementById("boldButton");
  var italicButton = document.getElementById("italicButton");
  var underlineButton = document.getElementById("underlineButton");
  var policeUpButton = document.getElementById("policeUpButton");
  var policeDownButton = document.getElementById("policeDownButton");

  //Stocker le type d'item en cours de création
  var itemEnCoursDeCration = document.getElementById("itemTitre");

  //chargement de la zone par défaut
  itemEnCoursDeCration.classList.add("active");
  blockParametrageDonneeVariable.style.display  = "none";
  blockParametrageDonneeCalculee.style.display  = "none";
  blockParametrageImage.style.display  = "none";
  blockParametrageText.style.display  = "block";


  //Gestion si click sur un item du menu de menu de droite
  $('.item').click(function(event){

    itemEnCoursDeCration = event.target;

    //affichage dans le menu de droite d'un backgroundgris sur l'option
    resetMenuSelectedItem();
    event.target.classList.add("active");

    //Si l'item nécessite le block de paramétrage "Text"
    if( event.target.getAttribute("id") == "itemTitre" ||
    event.target.getAttribute("id") == "itemZoneTexte" ||
    event.target.getAttribute("id") == "itemQuestion"){
      blockParametrageDonneeVariable.style.display  = "none";
      blockParametrageDonneeCalculee.style.display  = "none";
      blockParametrageImage.style.display  = "none";
      blockParametrageText.style.display  = "block";
    }

    //Si l'item nécessite le block de paramétrage "Donnée Variable"
    if(event.target.getAttribute("id") == "itemDonneeVariable"){
      blockParametrageText.style.display  = "none";
      blockParametrageImage.style.display  = "none";
      blockParametrageDonneeVariable.style.display  = "block";
      blockParametrageDonneeCalculee.style.display  = "none";

      //Déterminer quel block de paramétrage de donnée variable afficher
      typeDonnerClick();

    }

    //Si l'item nécessite le block de paramétrage "Donnée Calculee"
    if(event.target.getAttribute("id") == "itemDonneeCalculee"){
      blockParametrageText.style.display  = "none";
      blockParametrageImage.style.display  = "none";
      blockParametrageDonneeVariable.style.display  = "none";
      blockParametrageDonneeCalculee.style.display  = "block";
    }

    //Si l'item nécessite le block de paramétrage "Image"
    if(event.target.getAttribute("id") == "itemImage"){
      blockParametrageText.style.display  = "none";
      blockParametrageDonneeVariable.style.display  = "none";
      blockParametrageDonneeCalculee.style.display  = "none";
      blockParametrageImage.style.display  = "block";
    }
  });

  //Au clique sur le bouton, ajouter l'item à la zone de création
  boutonAjouter.onclick = function() {
    ajouterElement(itemEnCoursDeCration);
  };

  //Au clique sur le bouton, supprimer l'item de la zone de création
  boutonSupprimer.onclick = function() {
    supprimerElement(itemEnCoursDeCration);
  };

  //Au clique sur le bouton, ajouter l'item block de donnée variable
  boutonAjouterDonneeVariable.onclick = function() { ajouterBlockDonneeVariable(); };

  //Toggle fontWeight
  boldButton.onclick = function(){
    isBoldSelected = !isBoldSelected;

    if(isBoldSelected){
      this.classList.remove("btn-light");
      this.classList.add("btn-secondary");
    } else {
      this.classList.remove("btn-secondary");
      this.classList.add("btn-light");
    }
  };

  //Toggle fontStyle
  italicButton.onclick = function(){
    isItalicSelected = !isItalicSelected;

    if(isItalicSelected){
      this.classList.remove("btn-light");
      this.classList.add("btn-secondary");
    } else {
      this.classList.remove("btn-secondary");
      this.classList.add("btn-light");
    }
  };

  //Text Decoration
  underlineButton.onclick = function(){
    isUnderlineSelected = !isUnderlineSelected;

    if(isUnderlineSelected){
      this.classList.remove("btn-light");
      this.classList.add("btn-secondary");
    } else {
      this.classList.remove("btn-secondary");
      this.classList.add("btn-light");
    }
  };

  policeUpButton.onclick = function(){
    if(policeSize < fontSize.length)
      policeSize++;
  };

  policeDownButton.onclick = function(){
    if(policeSize > 0)
      policeSize--;
  };

  $('#buttonFakeInputFile').bind("click" , function () {
    $('#html_btn').click();
  });

  $('#formCreationEnonce').submit(function(event){
    event.preventDefault();

    enregistrerQuestions(this, handleEnregistrerQuestions);

    // enregistrerQuestions(handleEnregistrerQuestions, function(data){
    //   console.log(data);
    //   console.log("enregistrerQuestionsAfter");
    //   // if(validerEnonce()){
    //   //   console.log("validerEnonce");
    //   //   this.submit();
    //   // }
    // });

  });

});

function resetMenuSelectedItem(){
  document.getElementById("itemTitre").classList.remove("active");
  document.getElementById("itemZoneTexte").classList.remove("active");
  document.getElementById("itemDonneeVariable").classList.remove("active");
  document.getElementById("itemDonneeCalculee").classList.remove("active");
  document.getElementById("itemQuestion").classList.remove("active");
  document.getElementById("itemImage").classList.remove("active")
}

//Renvoie TRUE si le bouton radio "Valeur a valeur" est coché
function isRadioValeurParValeurChecked(){
  return document.getElementById("itemTypeDonneeValeurAValeur").checked;
}

//Renvoie TRUE si le bouton radio "Interval" est coché
function isRadioIntervalChecked(){
  return document.getElementById("itemTypeDonneeInterval").checked;
}

//Change le block de paramétrage en fonction du type de donnée séléctionné
function typeDonnerClick() {

  //Récupérer les éléments de l'ihm nécessaire
  var blockParametrageValeurAValeur = document.getElementById("blockParametrageValeurAValeur");
  var blockParametrageInterval = document.getElementById("blockParametrageInterval");

  //Comportement à appliquer
  if(isRadioValeurParValeurChecked()){
    blockParametrageInterval.style.display  = "none";
    blockParametrageValeurAValeur.style.display  = "block";
    boutonAjouterDonneeVariable.style.display = "block"
  } else if(isRadioIntervalChecked()){
    blockParametrageValeurAValeur.style.display  = "none";
    blockParametrageInterval.style.display  = "block";
    boutonAjouterDonneeVariable.style.display = "none"
  }
}

//Ajouter un item séléctionné et paramétré à la page de création (énoncé)
function ajouterElement(typeItem) {

  //Récupérer les éléments de l'ihm nécessaire
  var page_creation = document.getElementById("page_creation");
  var itemTitre = typeItem.getAttribute("id");
  var itemValeur = document.getElementById("itemValeur").value;
  var itemCouleur = document.getElementById("frenchColor").value;
  var itemSource = document.getElementById("html_btn");
  var itemDescription = document.getElementById("itemDescription");
  var itemLargeur = document.getElementById("itemLargeur");
  var itemHauteur = document.getElementById("itemHauteur");

  //Différent comportement à appliquer en fonction du type d'item à ajouter
  switch (itemTitre) {

    //Si l'item à ajouter est un "Titre"
    case "itemTitre":
      var newTitre = document.createElement('h1');
      newTitre.id = 'titre'+numItem;
      newTitre.name = 'item'+numItem;
      newTitre.style.fontSize = fontSize[policeSize];
      newTitre.style.color = itemCouleur;
      newTitre.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
      newTitre.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
      newTitre.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
      newTitre.appendChild(document.createTextNode(itemValeur));
    break;

    //Si l'item à ajouter est une "Zone de texte"
    case "itemZoneTexte":
      var newTitre = document.createElement('p');
      newTitre.id = 'zonedetext'+numItem;
      newTitre.name = 'item'+numItem;
      newTitre.style.fontSize = fontSize[policeSize];
      newTitre.style.color = itemCouleur;
      newTitre.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
      newTitre.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
      newTitre.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
      newTitre.style.display = "inline";
      newTitre.appendChild(document.createTextNode(itemValeur));
      contientText = true;
    break;

    //Si l'item à ajouter est une "Donnée Variable"
    case "itemDonneeVariable":
      var newTitre = document.createElement('data');
      newTitre.id = '##' + recupererIdTypeDonneeAjoute() + '##';
      newTitre.name = 'item'+numItem;
      newTitre.style.fontSize = fontSize[policeSize];
      newTitre.style.color = itemCouleur;
      newTitre.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
      newTitre.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
      newTitre.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
      newTitre.style.display = "inline";
      newTitre.appendChild(document.createTextNode(recupererLibelleTypeDonneeAjoute("selectTypeDonnee")));
      contientDonneeVariable = true;
    break;

    //Si l'item à ajouter est une "Donnée Calculée"
    case "itemDonneeCalculee":
      var newTitre = document.createElement('calculated_data');
      newTitre.id = '¤¤' + recupererIdTypeDonneeAjoute() + '¤¤';
      newTitre.name = 'item'+numItem;
      newTitre.style.fontSize = fontSize[policeSize];
      newTitre.style.color = itemCouleur;
      newTitre.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
      newTitre.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
      newTitre.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
      newTitre.style.display = "inline";
      newTitre.appendChild(document.createTextNode(recupererLibelleTypeDonneeAjoute("selectTypeDonneeCalculee")));
    break;

    //Si l'item à ajouter est une "Question"
    case "itemQuestion":
      numQR++;

      var newTitre = document.createElement('div');

      var question = document.createElement('span');
      question.id = 'question_' + numQR;
      newTitre.name = 'item'+numQR;
      question.style.fontSize = fontSize[policeSize];
      question.style.color = itemCouleur;
      question.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
      question.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
      question.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
      question.style.display = "inline";
      question.appendChild(document.createTextNode(itemValeur));

      //Appel de la fonction ajoutant la question à la base de donnée
      ajouterNouvelleQuestion(itemValeur);

      //Ajout d'un champ réponse associé
      var reponse = document.createElement('input');
      reponse.id = 'reponse_' + numQR;
      reponse.name = 'item' + numQR;
      reponse.type = 'text';
      reponse.placeholder = "Renseigner ici votre réponse";
      reponse.style.display = "inline";
      reponse.pattern="[0-9]+([\,|\.][0-9]+)?";
      reponse.step="0.01";

      newTitre.appendChild(question);
      newTitre.appendChild(reponse);

      contientQuestion = true;
    break;

    //Si l'item à ajouter est une "Image"
    case "itemImage":
      var newTitre = document.createElement('img');
      newTitre.id = 'image'+numItem;
      newTitre.name = 'item'+numItem;
      newTitre.alt = itemDescription.value;

    //Attendre que l'immage soit chargée pour l'afficher
    var reader = new FileReader();
    reader.addEventListener('load', function () {
      newTitre.src = reader.result;

      if(!empty(itemLargeur.value))
      newTitre.width = itemLargeur.value;

      if(!empty(itemHauteur.value))
      newTitre.height = itemHauteur.value;
    });

    reader.readAsDataURL(itemSource.files[0]);
    break;

    //Comportement par defaut
    default:
    console.log("Une erreur est survenue");
  }

  page_creation.appendChild(newTitre);

  itemASuppr.push(newTitre);
  numItem++;
}

//Supprimer le dernier élément ajouté à la zone de text
function supprimerElement(typeItem){

  //Supression du tableau des question si l'item a supprimer est une question
  if(typeItem.getAttribute("id") == "itemQuestion"){
    tableauQuestions.pop();
  }

  var page_creation = document.getElementById("page_creation");
  page_creation.removeChild(itemASuppr[itemASuppr.length - 1]);
  itemASuppr.pop();

}

//Ajoute un block d'insertion de donnée "Valeur à valeur"
function ajouterBlockDonneeVariable(){

  //Simulation d'une variable globale
  if( typeof idInput == 'undefined' ) { idInput = 0; }
  idInput++;

  //Récupérer les éléments de l'ihm nécessaire
  var blockParametrageValeurAValeur = document.getElementById("blockParametrageValeurAValeur");
  var newDivDonneeVariable = document.createElement('div');
  var newLabelDonneeVariable = document.createElement('label');
  var newInputDonneeVariable = document.createElement('input');

  newDivDonneeVariable.classList.add("form-group");

  //Ajout d'un label
  newLabelDonneeVariable.id = 'labelDonneeVariable';
  newLabelDonneeVariable.appendChild(document.createTextNode("Valeur : "));

  //Ajout de l'input
  newInputDonneeVariable.id = 'inputDonneeVariable'+idInput;
  //newInputDonneeVariable.appendChild(document.createTextNode(""));
  newInputDonneeVariable.classList.add("form-control");


  //Ajout des éléments au block de paramétrage des items
  newDivDonneeVariable.appendChild(newLabelDonneeVariable);
  newDivDonneeVariable.appendChild(newInputDonneeVariable);
  blockParametrageValeurAValeur.appendChild(newDivDonneeVariable);

}

//Récupère le code HTML de la page de création et l'insère comme valeur de champ "hidden pour la méthode POST
function validerEnonce(){
  var retour;
  if(numItem > 0){
    if(contientText){
      if(contientDonneeVariable){
        if(contientQuestion){

          var enonceCreer = document.getElementById('page_creation').innerHTML;
          var inputEnonceCreer = document.getElementById('enonceCreer');
          inputEnonceCreer.value = enonceCreer;

          retour = true;

        } else {
          alert("Création de l'énoncé impossible : ajoutez au moins une question !");
          retour = false;
        }
      } else {
        alert("Création de l'énoncé impossible : ajoutez au moins une donné variable !");
        retour = false;
      }
    } else {
      alert("Création de l'énoncé impossible : ajoutez au moins un texte explicatif !");
      retour = false;
    }
  } else {
    alert("Création de l'énoncé impossible : ajoutez au moins un élément !");
    retour = false;
  }

  return retour;
}

//Récupère le code HTML de la page de création et l'insère comme valeur de champ "hidden pour la méthode POST
function enregistrerQuestions(form , callback){

  $.ajax({
    type: "POST",
    url: './ajax/ajoutQuestion.ajax.php',
    data : {tableauQuestions: JSON.stringify(tableauQuestions)},
    success: function(data){
      creationEnonceReady = validerEnonce();
      callback(form);
    },
    error: function(data){
      console.log(data);
      alert("Une erreur s'est produite :/");
      creationEnonceReady = false;
      callback();
    }
  });

}

function handleEnregistrerQuestions(form){
  form.submit();
}

//Appel du fichier AJAX afin d'ajouter un nouveau type de donnée dans la base
function ajouterNouveauTypeDonnee(){

  //Récupérer les éléments de l'ihm nécessaire
  var newTypeDonnee = document.getElementById("newTypeDonnee").value;
  var inputDonneeVariable = document.getElementById("inputDonneeVariable0").value;
  var borneInferieurInterval = document.getElementById("borneInferieurInterval").value;
  var borneSuperieurInterval = document.getElementById("borneSuperieurInterval").value;
  var pasInterval = document.getElementById("pasInterval").value;

  //Si le libellé donné pour le type de donnée n'est pas vide
  if( newTypeDonnee != "" &&
  inputDonneeVariable != "" ||
  (
    borneInferieurInterval = "" && borneSuperieurInterval != "" && pasInterval != ""
  )
){

  $.ajax({
    type: "POST",
    url: './ajax/ajoutTypeDonnee.ajax.php',
    dataType: "json",
    data : { newTypeDonnee: newTypeDonnee },
    success: function() {
      //Appel de la fonction d'ajout des donnée variables associé
      ajouterNouvelleDonneeVariable();
      refreshSelectTypeDonnee(newTypeDonnee,"selectTypeDonnee");
    }
  });

  return true;
} else {
  return false;
}
}


//Permet de mettre à jour le liste déroulante avec le nouveau type de donnée qui vient d'être ajouté
function refreshSelectTypeDonnee(newTypeDonnee,target){

  var selectTypeDonnee =  document.getElementById(target);

  var option = document.createElement("option");
  option.value = "<?php if( isset($_SESSION['newIdTypeDonne'])){ echo $_SESSION['newIdTypeDonne']; } ?>";
  option.text = newTypeDonnee;
  selectTypeDonnee.appendChild(option);

  alert("Un nouveau type de donnée à été ajouté à la liste !");
}

//Descide du comportement à appliquer en fonction du type de donnée à ajouter
function ajouterNouvelleDonneeVariable(){

  //Si se sont des données à ajouter via un interval
  if(isRadioIntervalChecked()){
    ajouterNouvelleDonneeVariableViaInterval();
  }
  //Si se sont des données à ajouter valeur à valeur
  else if(isRadioValeurParValeurChecked()){
    ajouterNouvelleDonneeVariableValeurAValeur();
  }

}

//Appel du fichier AJAX afin d'ajouter les nouvelles donnée variable associé au nouveau type de donnée via un interval
function ajouterNouvelleDonneeVariableViaInterval(){

  //Récupérer les éléments de l'ihm nécessaire
  var borneInferieurInterval = document.getElementById("borneInferieurInterval").value;
  var borneSuperieurInterval = document.getElementById("borneSuperieurInterval").value;
  var pasInterval = document.getElementById("pasInterval").value;

  //Si les champs nécessaire ne sont pas vide
  if(borneInferieurInterval != "" && borneSuperieurInterval != "" && pasInterval != ""){

    //Appel du fichier AJAX avec les paramètres passé grace à la méthode POST
    $.post("./ajax/ajoutDonneeVariableViaInterval.ajax.php", {
      borneInferieurInterval: borneInferieurInterval ,
      borneSuperieurInterval: borneSuperieurInterval ,
      pasInterval: pasInterval
    });
  }
}

//Appel du fichier AJAX afin d'ajouter les nouvelles donnée variable associé au nouveau type de donnée valeur après valeur
function ajouterNouvelleDonneeVariableValeurAValeur(){

  //Récupérer les éléments de l'ihm nécessaire
  var tab = document.getElementsByTagName('input');
  var liste = [];

  for(var i=0; i<tab.length; i++) {

    //Récupérer toutes les valeurs possible de la donnée variable
    if ( tab[i].id.substring(0, 19) == 'inputDonneeVariable' ) {
      liste.push(document.getElementById(tab[i].id).value);
    }

  }

  //Si la liste des donnée variable à ajouter n'est pas vide
  if(liste.length != 0){

    //Appel du fichier AJAX avec les paramètres passé grace à la méthode POST
    $.post("./ajax/ajoutDonneeVariableValeurAValeur.ajax.php", {
      liste: liste
    });
  }

}

//Retourne l'id du type de donnée séléctionné
function recupererIdTypeDonneeAjoute(){

  //Récupérer les éléments de l'ihm nécessaire
  var typeDonnee = document.getElementById("selectTypeDonnee");

  //Récupérer la valeur de l'item séléctionné dans la liste déroulante
  typeDonnee = typeDonnee.options[typeDonnee.selectedIndex].value;

  //Si l'item séléctionné dans la liste est le 1er ("Créer un nouveau type")
  if(typeDonnee == "0"){
    //Retourner le dernier idType inséré dans la table TypeDonnee de la base de donnée (via la variable de session venant du TypeDonneeManager)
    return "<?php if(isset($_SESSION['newIdTypeDonne'])){ echo $_SESSION['newIdTypeDonne']; } else { echo '0'; }?>";
  } else {
    //Retourner l'id du type séléctionné dans la liste déroulante
    return typeDonnee;
  }

}


//Retourne le libellé du type de donnée séléctionné
function recupererLibelleTypeDonneeAjoute(target){

  //Récupérer les éléments de l'ihm nécessaire
  var typeDonnee = document.getElementById(target);

  //Récupérer le libellé du type de donnée séléctionné / inséré
  typeDonneeValue = typeDonnee.options[typeDonnee.selectedIndex].value;
  typeDonneeText = typeDonnee.options[typeDonnee.selectedIndex].text;

  //Retourne le type de donné séléctionné
  return typeDonneeText;
}

//Appel du fichier AJAX afin d'ajouter une nouvelle question dans la base de donnée
function ajouterNouvelleQuestion(libelle){

  //Si le libellé de la question n'est pas vide
  if(libelle != ""){
    tableauQuestions.push(libelle);
  }

}

//Retourne un id pour la question/réponse autoincrémenté a chaque fois
function recupererNumQuestionReponseAjax(){

    $.ajax({
      type: "POST",
      url: './ajax/recupererNumQuestionReponse.ajax.php',
      dataType: "json",
      success: function(init_numQRLoc) {
          numQR = init_numQRLoc;
      }
    });

}

//Déclanché si click sur un boutton d'ajout de paramètres
function ajouterParametresCalculeDonnee() {

  if( typeof newId == 'undefined' ) { tableauNumParams.push(0); newId = 1; } else { newId++; }

  //Création et paramétrage d'une nouvelle liste déroulante
	var newParam = document.createElement('select');
	newParam.id = "paramCalcul" + newId;
  newParam.classList.add("form-control", "paramCalcul");

  //Ajout de la nouvelle liste déroulante
	var referenceNode = document.getElementById("paramCalcul" + (newId-1));
	referenceNode.parentNode.insertBefore(newParam, referenceNode.nextSibling);

  //Appel de la fonction d'appel ajax
	ajouterNouveauParams(newParam);

  //Ajout de l'id ajouter au tableau des id
  tableauNumParams.push(newId);
}

//Appel du fichier AJAX afin d'ajouter une nouvelle collonne de paramètre
function ajouterNouveauParams(newParam) {

  $.ajax({
    type: "POST",
    url: './ajax/ajoutPamametresCorrection.ajax.php',
    dataType: "json",
    success: function(array) {
      //Appel à la fonction d'ajout d'option
      populateSelect(array,newParam);
    }
  });

}

//Permet d'ajouter des option à la liste déroulante à partir d'un tableau JSON
function populateSelect(array, newParam){

	for (var i = 0; i < array.length; i++) {

      //Création des différentes option de la liste déroulante selon le tableau
			var option = document.createElement("option");
			option.value = array[i].idTypeDonnee;
			option.text = array[i].libelleTypeDonnee;

      //Ajout des option à la liste déroulante
			newParam.appendChild(option);
	}
}

//Appeler pour enregistrer les élémennts de calcul de donnée
function validerCalcul(){

    //Récupérer le libellé de la donnée calculée
    var libelleDonneeCalculee = document.getElementById("libelleDonneeCalculee").value;

    //Récupérer le nom de la fonction de correction
    var nomFormuleCalcul = document.getElementById("formuleCalcul");
    nomFormuleCalcul = nomFormuleCalcul.options[nomFormuleCalcul.selectedIndex].value;

    //Récupérer les paramètres à passer à la fonction de correction
    var tableauIdParams = Array();
    var listeElementsParams = document.getElementsByClassName('paramCalcul');

    //Pour chaque paramètres
    for (var i = 0; i < listeElementsParams.length; i++) {
      //Récupérer l'id de la donnée variable à utiliser
      var idDonneCalculeeParamsTemp = document.getElementById(listeElementsParams[i].id);
      idDonneCalculeeParamsTemp = idDonneCalculeeParamsTemp.options[idDonneCalculeeParamsTemp.selectedIndex].value;

      //Ajouter cette id au tableau des paramètres de correction de la question
      tableauIdParams.push(idDonneCalculeeParamsTemp);
    }

    return ajoutDonneeCalculee(libelleDonneeCalculee,nomFormuleCalcul,tableauIdParams);

}

//Appel du fichier AJAX afin d'ajouter une nouvelle DonneeCalculee
function ajoutDonneeCalculee(libelleDonneeCalculee,nomFormuleCalcul,tableauIdParams) {

  $.ajax({
    type: "POST",
    url: './ajax/ajoutDonneeCalculee.ajax.php',
    dataType: "json",
    data :
      {
        libelleDonneeCalculee: libelleDonneeCalculee,
        nomFormuleCalcul: nomFormuleCalcul,
        tableauIdParams: tableauIdParams
      },
    success: function() {
      refreshSelectTypeDonnee(libelleDonneeCalculee,"selectTypeDonneeCalculee");
    }
  });

}
