const fontWeight = ["normal", "bold"];
const fontStyle = ["normal", "italic"];
const textDecoration = ["none", "underline"];
const fontSize = ["60%", "80%", "100%", "150%", "200%", "300%", "400%", "450%"];

let isBoldSelected = false;
let isItalicSelected = false;
let isUnderlineSelected = false;
let policeSize = 3;
let indexItemDonnee = 0;
const itemASuppr = [];
let numItem = 0;
let numQR = recupererNumQuestionReponseAjax();
let contientQuestion = false;
let contientDonneeVariable = false;
let contientText = false;
let creationEnonceReady = false;
let numImageVariable = 1;
let dejaPresent = false;
let debutTableauDonneeVariable = 0;

const tableauQuestions = []; //Création du tableau mémorisant les questions de l'énoncé
const tableauNumParams = []; //Création du tableau mémorisant les id des paramètres de chaque donnée calculée
const tableauDonneeVariable = [];

/* Constantes */
const EMPLACEMENT_IMAGES_FIXES = '/../public/images/fixes/';

const TOUCHE_SUPPR = "Delete";
const TOUCHE_ENTRER = "Enter";


//Objet Donnee Variable
function DonneeVariable(idType, libelle) {

    this.idType = idType;
    this.libelle = libelle;

    this.getIdType = function () {
        return this.idType;
    };

    this.getLibelle = function () {
        return this.libelle;
    }
}

let tooltip = function () {
    const id = 'tt';
    const top = 3;
    const maxw = 300;
    const speed = 10;
    const timer = 30;
    const endalpha = 95;
    let alpha = 0;
    let tt, c, h;


    return {
        show: function (v, w, target, callback) {
            let targetElement = document.getElementById(target);

            if (tt == null) {
                tt = document.createElement('div');
                tt.setAttribute('id', id);
                c = document.createElement('div');
                c.setAttribute('id', id + 'cont');
                tt.appendChild(c);
                targetElement.appendChild(tt);
                tt.style.opacity = '0';
                tt.style.filter = 'alpha(opacity=0)';
            }

            tt.style.top = (targetElement.offsetTop - 15) + 'px';
            tt.style.left = (targetElement.offsetLeft - 15) + 'px';

            tt.style.display = 'block';
            c.innerHTML = v;
            tt.style.zIndex = '10000';
            tt.style.width = w ? w + 'px' : 'auto';

            if (tt.offsetWidth > maxw) {
                tt.style.width = maxw + 'px'
            }
            h = parseInt(tt.offsetHeight) + top;
            clearInterval(tt.timer);
            tt.timer = setInterval(function () {
                tooltip.fade(1)
            }, timer);
            callback();
        },
        fade: function (d) {
            const a = alpha;
            if ((a !== endalpha && d === 1) || (a !== 0 && d === -1)) {
                let i = speed;
                if (endalpha - a < speed && d === 1) {
                    i = endalpha - a;
                } else if (alpha < speed && d === -1) {
                    i = a;
                }
                alpha = a + (i * d);
                tt.style.opacity = alpha * .01;
                tt.style.filter = 'alpha(opacity=' + alpha + ')';
            } else {
                clearInterval(tt.timer);
                if (d === -1) {
                    tt.style.display = 'none'
                }
            }
        },
        hide: function () {
            clearInterval(tt.timer);
            tt.timer = setInterval(function () {
                tooltip.fade(-1)
            }, timer);
        }
    };
}();
//Attendre que le document soit compvarement chargé
$(document).ready(function () {

    //Récupérer les éléments de l'ihm nécessaire
    const blockParametrageText = document.getElementById("blockParametrageText");
    const blockParametrageDonneeVariable = document.getElementById("blockParametrageDonneeVariable");
    const blockParametrageDonneeCalculee = document.getElementById("blockParametrageDonneeCalculee");
    const blockParametrageImage = document.getElementById("blockParametrageImage");
    const blockParametrageImageVariable = document.getElementById("blockParametrageImageVariable");
    const boutonAjouterDonneeVariable = document.getElementById("boutonAjouterDonneeVariable");
    const boutonAjouter = document.getElementById("boutonAjouter");
    const boutonSupprimer = document.getElementById("boutonSupprimer");
    const boldButton = document.getElementById("boldButton");
    const italicButton = document.getElementById("italicButton");
    const underlineButton = document.getElementById("underlineButton");
    const policeUpButton = document.getElementById("policeUpButton");
    const policeDownButton = document.getElementById("policeDownButton");

    const buttonFakeInputFile = document.getElementById("buttonFakeInputFile");
    const imageBrowser = document.getElementById("imageBrowser");
    const imageChoisi = document.getElementById("imageChoisi");
    const imageBlockChoisi = document.getElementById("imageBlockChoisi");

    const selectParamCalcul0 = document.getElementById("paramCalcul0");

    //Stocker le type d'item en cours de création
    let itemEnCoursDeCration = document.getElementById("itemTitre");

    //chargement de la zone par défaut
    itemEnCoursDeCration.classList.add("active");
    blockParametrageDonneeVariable.style.display = "none";
    blockParametrageDonneeCalculee.style.display = "none";
    blockParametrageImage.style.display = "none";
    blockParametrageImageVariable.style.display = "none";
    blockParametrageText.style.display = "block";


    //Gestion si click sur un item du menu de menu de droite
    $('.item').click(function (event) {

        itemEnCoursDeCration = event.target;

        //affichage dans le menu de droite d'un backgroundgris sur l'option
        resetMenuSelectedItem();
        event.target.classList.add("active");

        if (event.target.getAttribute("id") !== "itemDonneeCalculee") {
            let paramSuplementaires = document.getElementById("paramSuplementaires");
            while (paramSuplementaires.firstChild) {
                paramSuplementaires.removeChild(paramSuplementaires.firstChild);
            }
        }


        if (event.target.getAttribute("id") !== "itemDonneeVariable") {
            let paramSuplementaires = document.getElementById("parametrageValeurAValeurSuplementaire");
            while (paramSuplementaires.firstChild) {
                paramSuplementaires.removeChild(paramSuplementaires.firstChild);
            }
        }

        //Si l'item nécessite le block de paramétrage "Text"
        if (event.target.getAttribute("id") === "itemTitre" ||
            event.target.getAttribute("id") === "itemZoneTexte" ||
            event.target.getAttribute("id") === "itemQuestion") {
            blockParametrageDonneeVariable.style.display = "none";
            blockParametrageDonneeCalculee.style.display = "none";
            blockParametrageImage.style.display = "none";
            blockParametrageImageVariable.style.display = "none";
            blockParametrageText.style.display = "block";
        }

        //Si l'item nécessite le block de paramétrage "Donnée Variable"
        if (event.target.getAttribute("id") === "itemDonneeVariable") {
            blockParametrageText.style.display = "none";
            blockParametrageImage.style.display = "none";
            blockParametrageImageVariable.style.display = "none";
            blockParametrageDonneeVariable.style.display = "block";
            blockParametrageDonneeCalculee.style.display = "none";

            //Déterminer quel block de paramétrage de donnée variable afficher
            typeDonnerClick();

        }

        //Si l'item nécessite le block de paramétrage "Donnée Calculee"
        if (event.target.getAttribute("id") === "itemDonneeCalculee") {

            if (tableauDonneeVariable.length > 0) {
                blockParametrageText.style.display = "none";
                blockParametrageImage.style.display = "none";
                blockParametrageImageVariable.style.display = "none";
                blockParametrageDonneeVariable.style.display = "none";
                blockParametrageDonneeCalculee.style.display = "block";

                //Si une nouvelle donnée variable a été ajouté , mettre a jour le select
                if (!dejaPresent) {

                    for (let i = debutTableauDonneeVariable; i < tableauDonneeVariable.length; i++) {
                        const option = document.createElement("option");
                        option.value = tableauDonneeVariable[i].idType;
                        option.text = tableauDonneeVariable[i].libelle;
                        selectParamCalcul0.appendChild(option);
                    }

                    //Pout ne pas ajouter la même donnée au select au prochain passage
                    debutTableauDonneeVariable++;
                }

            } else {
                alert("Aucune donnée variable n'a été ajoutée à l'énoncé ! ");
                resetMenuSelectedItem();
            }
        }

        //Si l'item nécessite le block de paramétrage "Image"
        if (event.target.getAttribute("id") === "itemImage") {
            blockParametrageText.style.display = "none";
            blockParametrageDonneeVariable.style.display = "none";
            blockParametrageDonneeCalculee.style.display = "none";
            blockParametrageImage.style.display = "block";
            blockParametrageImageVariable.style.display = "none";
        }

        //Si l'item nécessite le block de paramétrage "Image"
        if (event.target.getAttribute("id") === "itemImageVariable") {
            blockParametrageText.style.display = "none";
            blockParametrageDonneeVariable.style.display = "none";
            blockParametrageDonneeCalculee.style.display = "none";
            blockParametrageImage.style.display = "none";
            blockParametrageImageVariable.style.display = "block";
        }
    });

    //Au clique sur le bouton, ajouter l'item à la zone de création
    boutonAjouter.onclick = function () {
        ajouterElement(itemEnCoursDeCration);
    };

    //Au clique sur le bouton, supprimer l'item de la zone de création
    boutonSupprimer.onclick = function () {
        supprimerElement(itemEnCoursDeCration);
    };

    //Au clique sur le bouton, ajouter l'item block de donnée variable
    boutonAjouterDonneeVariable.onclick = function () {
        ajouterBlockDonneeVariable();
    };

    //Toggle fontWeight
    boldButton.onclick = function () {
        isBoldSelected = !isBoldSelected;

        if (isBoldSelected) {
            this.classList.remove("btn-light");
            this.classList.add("btn-secondary");
        } else {
            this.classList.remove("btn-secondary");
            this.classList.add("btn-light");
        }
    };

    //Toggle fontStyle
    italicButton.onclick = function () {
        isItalicSelected = !isItalicSelected;

        if (isItalicSelected) {
            this.classList.remove("btn-light");
            this.classList.add("btn-secondary");
        } else {
            this.classList.remove("btn-secondary");
            this.classList.add("btn-light");
        }
    };

    //Text Decoration
    underlineButton.onclick = function () {
        isUnderlineSelected = !isUnderlineSelected;

        if (isUnderlineSelected) {
            this.classList.remove("btn-light");
            this.classList.add("btn-secondary");
        } else {
            this.classList.remove("btn-secondary");
            this.classList.add("btn-light");
        }
    };

    policeUpButton.onclick = function () {
        if (policeSize < fontSize.length)
            policeSize++;
        tooltip.show(policeSize, 28, "policeUpButton", function () {
            setTimeout(function () {
                tooltip.hide();
            }, 1000);
        });

    };

    policeDownButton.onclick = function () {
        if (policeSize > 1)
            policeSize--;
        tooltip.show(policeSize, 28, "policeDownButton", function () {
            setTimeout(function () {
                tooltip.hide();
            }, 1000);
        });
    };

    buttonFakeInputFile.onclick = function () {
        imageBrowser.style.display = "block";
    };

    $('#loadFolderTree').fileTree({
        root: EMPLACEMENT_IMAGES_FIXES,
        script: 'ajax/jqueryFileTree.ajax.php',
        multiFolder: false,
    }, function (file) {
        imageChoisi.value = file.replace(EMPLACEMENT_IMAGES_FIXES, "");
        imageBlockChoisi.style.display = "inline";
    });

    $('#formCreationEnonce').submit(function (event) {
        event.preventDefault();
        enregistrerQuestions(this, handleEnregistrerQuestions);
    });

    //Détécter les touche de clavier pressé
    $("#itemValeur").on('keyup', function (e) {
        //Si le touche "entrer" est pressée
        if (e.key === TOUCHE_ENTRER) {
            let itemValeur = $('#itemValeur');
            itemValeur.height(itemValeur.height() + 25);
        }

        //Si le touche "suppr" est pressée
        if (e.key === TOUCHE_SUPPR) {
            let itemValeur = $('#itemValeur');
            itemValeur.height(itemValeur.height() - 25);
        }
    });

});

function closeImageBrowser() {
    const imageBrowser = document.getElementById("imageBrowser");
    imageBrowser.style.display = "none";
}

function resetMenuSelectedItem() {
    document.getElementById("itemTitre").classList.remove("active");
    document.getElementById("itemZoneTexte").classList.remove("active");
    document.getElementById("itemDonneeVariable").classList.remove("active");
    document.getElementById("itemDonneeCalculee").classList.remove("active");
    document.getElementById("itemQuestion").classList.remove("active");
    document.getElementById("itemImage").classList.remove("active");
    document.getElementById("itemImageVariable").classList.remove("active");
    const itemDescriptionVariable = document.getElementById("itemDescriptionVariable");
    itemDescriptionVariable.style.borderColor = "#ced4da";
}

//Renvoie TRUE si le bouton radio "Valeur a valeur" est coché
function isRadioValeurParValeurChecked() {
    return document.getElementById("itemTypeDonneeValeurAValeur").checked;
}

//Renvoie TRUE si le bouton radio "Interval" est coché
function isRadioIntervalChecked() {
    return document.getElementById("itemTypeDonneeInterval").checked;
}

//Change le block de paramétrage en fonction du type de donnée séléctionné
function typeDonnerClick() {

    //Récupérer les éléments de l'ihm nécessaire
    const blockParametrageValeurAValeur = document.getElementById("blockParametrageValeurAValeur");
    const blockParametrageInterval = document.getElementById("blockParametrageInterval");
    const inputDonneeVariable = document.getElementById("inputDonneeVariable0");
    const borneInferieurInterval = document.getElementById("borneInferieurInterval");
    const borneSuperieurInterval = document.getElementById("borneSuperieurInterval");
    const pasInterval = document.getElementById("pasInterval");

//Comportement à appliquer
    if (isRadioValeurParValeurChecked()) {
        blockParametrageInterval.style.display = "none";
        blockParametrageValeurAValeur.style.display = "block";
        boutonAjouterDonneeVariable.style.display = "block";
        inputDonneeVariable.setAttribute("required", "");
        borneInferieurInterval.removeAttribute("required");
        borneInferieurInterval.setCustomValidity('');
        borneSuperieurInterval.removeAttribute("required");
        borneSuperieurInterval.setCustomValidity('');
        pasInterval.removeAttribute("required");
        pasInterval.setCustomValidity('');
    } else if (isRadioIntervalChecked()) {
        blockParametrageValeurAValeur.style.display = "none";
        blockParametrageInterval.style.display = "block";
        boutonAjouterDonneeVariable.style.display = "none";
        inputDonneeVariable.removeAttribute("required");
        inputDonneeVariable.setCustomValidity('');
        borneInferieurInterval.setAttribute("required", "");
        borneSuperieurInterval.setAttribute("required", "");
        pasInterval.setAttribute("required", "");
    }
}

//Ajouter un item séléctionné et paramétré à la page de création (énoncé)
function ajouterElement(typeItem) {

    //Récupérer les éléments de l'ihm nécessaire
    const page_creation = document.getElementById("page_creation");
    const itemTitre = typeItem.getAttribute("id");
    const itemValeur = document.getElementById("itemValeur").value;
    const itemCouleur = document.getElementById("frenchColor").value;
    const itemDescription = document.getElementById("itemDescription");
    const itemDescriptionVariable = document.getElementById("itemDescriptionVariable");
    const itemLargeur = document.getElementById("itemLargeur");
    const itemHauteur = document.getElementById("itemHauteur");
    let newTitre;

    //Différent comportement à appliquer en fonction du type d'item à ajouter
    switch (itemTitre) {

        //Si l'item à ajouter est un "Titre"
        case "itemTitre":
            newTitre = document.createElement('h1');
            newTitre.id = 'titre' + numItem;
            newTitre.name = 'item' + numItem;
            newTitre.style.fontSize = fontSize[policeSize - 1];
            newTitre.style.color = itemCouleur;
            newTitre.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
            newTitre.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
            newTitre.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
            newTitre.appendChild(document.createTextNode(itemValeur));
            break;

        //Si l'item à ajouter est une "Zone de texte"
        case "itemZoneTexte":
            newTitre = document.createElement('pre');
            newTitre.id = 'zonedetext' + numItem;
            newTitre.name = 'item' + numItem;
            newTitre.style.fontSize = fontSize[policeSize - 1];
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
            newTitre = document.createElement('data');
            newTitre.id = '##' + recupererIdTypeDonneeAjoute() + '##' + indexItemDonnee;
            newTitre.name = 'item' + numItem;
            newTitre.style.fontSize = fontSize[policeSize];
            newTitre.style.color = itemCouleur;
            newTitre.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
            newTitre.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
            newTitre.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
            newTitre.style.display = "inline";
            newTitre.appendChild(document.createTextNode(recupererTypeDonneeAjoute("selectTypeDonnee")[1]));
            contientDonneeVariable = true;
            indexItemDonnee = indexItemDonnee + 1;

            let idTypeDonne = recupererIdTypeDonneeAjoute();
            let index = 0;
            dejaPresent = false;

            //Recherche si idTypeDonne déjà présent dans tableau des donneVariable ajouté
            while (index < tableauDonneeVariable.length && !dejaPresent) {

                if (tableauDonneeVariable[index].getIdType() === idTypeDonne) {
                    dejaPresent = true;
                }

                index++;
            }

            if (!dejaPresent) {
                $.ajax({
                    type: "POST",
                    url: './ajax/recupererTypeDonneById.ajax.php',
                    data: {idType: idTypeDonne},
                    success: function (typeDonnee) {

                        typeDonnee = JSON.parse(typeDonnee);

                        tableauDonneeVariable.push(
                            new DonneeVariable(
                                typeDonnee.idType,
                                typeDonnee.libelle
                            ));

                    }
                });

            }

            break;

        //Si l'item à ajouter est une "Donnée Calculée"
        case "itemDonneeCalculee":
            const typeDonne = recupererTypeDonneeAjoute("selectTypeDonneeCalculee");
            newTitre = document.createElement('calculated_data');
            newTitre.id = '¤¤' + typeDonne[0] + '¤¤' + indexItemDonnee;
            newTitre.name = 'item' + numItem;
            newTitre.style.fontSize = fontSize[policeSize];
            newTitre.style.color = itemCouleur;
            newTitre.style.fontWeight = fontWeight[isBoldSelected ? 1 : 0];
            newTitre.style.fontStyle = fontStyle[isItalicSelected ? 1 : 0];
            newTitre.style.textDecoration = textDecoration[isUnderlineSelected ? 1 : 0];
            newTitre.style.display = "inline";
            newTitre.appendChild(document.createTextNode(typeDonne[1]));
            indexItemDonnee = indexItemDonnee + 1;
            break;

        //Si l'item à ajouter est une "Question"
        case "itemQuestion":
            numQR++;

            newTitre = document.createElement('div');

            const question = document.createElement('span');
            question.id = 'question_' + numQR;
            newTitre.name = 'item' + numQR;
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
            const reponse = document.createElement('input');
            reponse.id = 'reponse_' + numQR;
            reponse.name = 'item' + numQR;
            reponse.type = 'text';
            reponse.placeholder = "Renseigner ici votre réponse";
            reponse.style.display = "inline";
            reponse.pattern = "[0-9]+([\,|\.][0-9]+)?";
            reponse.step = "0.01";

            newTitre.appendChild(question);
            newTitre.appendChild(reponse);

            contientQuestion = true;
            break;

        //Si l'item à ajouter est une "Image"
        case "itemImage":
            newTitre = document.createElement('img');
            newTitre.id = 'image' + numItem;
            newTitre.name = 'item' + numItem;
            newTitre.alt = itemDescription.value;
            newTitre.src = "./public/images/fixes/" + imageChoisi.value;

            if (itemLargeur.value !== '')
                newTitre.width = itemLargeur.value;

            if (itemHauteur.value !== '')
                newTitre.height = itemHauteur.value;

            break;

        //Si l'item à ajouter est une "Image Variable"
        case "itemImageVariable":
            if (itemDescriptionVariable.value !== "") {
                newTitre = document.createElement('img');
                newTitre.id = 'imageVariable_' + numImageVariable;
                newTitre.classList.add("imageVariable");
                newTitre.name = 'item' + numItem;
                newTitre.alt = itemDescriptionVariable.value;

                if (itemLargeur.value !== '')
                    newTitre.width = itemLargeur.value;

                if (itemHauteur.value !== '')
                    newTitre.height = itemHauteur.value;

                numImageVariable++;
            }

            break;
    }

    if (itemDescriptionVariable.value === "") {
        itemDescriptionVariable.style.borderColor = "red";
    }

    page_creation.appendChild(newTitre);

    itemASuppr.push(newTitre);
    numItem++;
}

//Supprimer le dernier élément ajouté à la zone de text
function supprimerElement(typeItem) {

    //Supression du tableau des question si l'item a supprimer est une question
    if (typeItem.getAttribute("id") === "itemQuestion") {
        tableauQuestions.pop();
    }

    if (typeItem.getAttribute("id") === "itemImageVariable" && numImageVariable > 1) {
        numImageVariable--;
    }

    const page_creation = document.getElementById("page_creation");
    page_creation.removeChild(itemASuppr[itemASuppr.length - 1]);
    itemASuppr.pop();

    if (indexItemDonnee > 0) {
        indexItemDonnee = indexItemDonnee - 1;
    }

}

//Ajoute un block d'insertion de donnée "Valeur à valeur"
function ajouterBlockDonneeVariable() {

    //Simulation d'une variable globale
    let idInput;
    if (typeof idInput == 'undefined') {
        idInput = 0;
    }
    idInput++;

    //Récupérer les éléments de l'ihm nécessaire
    const parametrageValeurAValeurSuplementaire = document.getElementById("parametrageValeurAValeurSuplementaire");
    const newDivDonneeVariable = document.createElement('div');
    const newLabelDonneeVariable = document.createElement('label');
    const newInputDonneeVariable = document.createElement('input');

    newDivDonneeVariable.classList.add("form-group");

    //Ajout d'un label
    newLabelDonneeVariable.id = 'labelDonneeVariable';
    newLabelDonneeVariable.appendChild(document.createTextNode("Valeur : "));

    //Ajout de l'input
    newInputDonneeVariable.id = 'inputDonneeVariable' + idInput;
    newInputDonneeVariable.classList.add("form-control", "inputDonneeVariable");


    //Ajout des éléments au block de paramétrage des items
    newDivDonneeVariable.appendChild(newLabelDonneeVariable);
    newDivDonneeVariable.appendChild(newInputDonneeVariable);
    parametrageValeurAValeurSuplementaire.appendChild(newDivDonneeVariable);

}

//Récupère le code HTML de la page de création et l'insère comme valeur de champ "hidden pour la méthode POST
function validerEnonce() {
    let retour;
    if (numItem > 0) {
        if (contientText) {
            if (contientDonneeVariable) {
                if (contientQuestion) {

                    const enonceCreer = document.getElementById('page_creation').innerHTML;
                    const inputEnonceCreer = document.getElementById('enonceCreer');
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
function enregistrerQuestions(form, callback) {

    $.ajax({
        type: "POST",
        url: './ajax/ajoutQuestion.ajax.php',
        data: {tableauQuestions: JSON.stringify(tableauQuestions)},
        success: function () {
            creationEnonceReady = validerEnonce();
            callback(form);
        },
        error: function () {
            alert("Une erreur s'est produite :/");
            creationEnonceReady = false;
            callback();
        }
    });

}

function handleEnregistrerQuestions(form) {
    form.submit();
}

function ajouterNouveauTypeDonnee() {

    //Récupérer les éléments de l'ihm nécessaire
    const newTypeDonnee = document.getElementById("newTypeDonnee");

    if (newTypeDonnee.value !== "") {
        const inputDonneeVariable = document.getElementById("inputDonneeVariable0");

        //Si on ajoute une donnée variable valeur a valeur
        if (isRadioValeurParValeurChecked()) {
            //Si la première valeur est vide
            if (inputDonneeVariable.value === "") {
                inputDonneeVariable.setCustomValidity("Entrez au moins une valeur");
            } else {
                ajouterTypeDonneAjax(newTypeDonnee.value);
            }
        } else if (isRadioIntervalChecked()) {
            const borneInferieurInterval = document.getElementById("borneInferieurInterval");
            const borneSuperieurInterval = document.getElementById("borneSuperieurInterval");
            const pasInterval = document.getElementById("pasInterval");

            if (borneInferieurInterval.value === "") {
                borneInferieurInterval.setCustomValidity("Vous devez saisir une valeur");
            } else if (borneSuperieurInterval.value === "") {
                borneSuperieurInterval.setCustomValidity("Vous devez saisir une valeur");
            } else if (pasInterval.value === "") {
                pasInterval.setCustomValidity("Vous devez saisir une valeur");
            } else {
                if (parseInt(borneInferieurInterval.value) > parseInt(borneSuperieurInterval.value)) {
                    borneSuperieurInterval.setCustomValidity("La borne supérieur est inférieure à la borne inférieure");
                } else if (parseInt(pasInterval.value) > (parseInt(borneSuperieurInterval.value) - parseInt(borneInferieurInterval.value))) {
                    pasInterval.setCustomValidity("Le pas est invalide pour l'interval donné");
                } else {
                    ajouterTypeDonneAjax(newTypeDonnee.value);
                }
            }
        }
    } else {
        newTypeDonnee.setCustomValidity("Entrez un nom pour la donnée variable");
    }
}

//Appel du fichier AJAX afin d'ajouter un nouveau type de donnée dans la base
function ajouterTypeDonneAjax(newTypeDonnee) {

    $.ajax({
        type: "POST",
        dataType: "json",
        url: './ajax/ajoutTypeDonnee.ajax.php',
        data: {newTypeDonnee: newTypeDonnee},
        success: function () {
            //Appel de la fonction d'ajout des donnée variables associé
            ajouterNouvelleDonneeVariable();
            refreshSelectTypeDonnee(newTypeDonnee, "selectTypeDonnee");
        }
    });
}


//Permet de mettre à jour le liste déroulante avec le nouveau type de donnée qui vient d'être ajouté
function refreshSelectTypeDonnee(newTypeDonnee, target) {

    recupererIdTypeDonneeAjouteAjaxa(function (newIdTypeDonne) {

        const selectTypeDonnee = document.getElementById(target);
        const option = document.createElement("option");
        option.value = newIdTypeDonne;
        option.text = newTypeDonnee;
        selectTypeDonnee.appendChild(option);

        alert("Un nouveau type de donnée a été ajouté à la liste !");
    });
}

//Descide du comportement à appliquer en fonction du type de donnée à ajouter
function ajouterNouvelleDonneeVariable() {

    //Si se sont des données à ajouter via un interval
    if (isRadioIntervalChecked()) {
        ajouterNouvelleDonneeVariableViaInterval();
    }
    //Si se sont des données à ajouter valeur à valeur
    else if (isRadioValeurParValeurChecked()) {
        ajouterNouvelleDonneeVariableValeurAValeur();
    }

}

//Appel du fichier AJAX afin d'ajouter les nouvelles donnée variable associé au nouveau type de donnée via un interval
function ajouterNouvelleDonneeVariableViaInterval() {

    //Récupérer les éléments de l'ihm nécessaire
    const borneInferieurInterval = document.getElementById("borneInferieurInterval").value;
    const borneSuperieurInterval = document.getElementById("borneSuperieurInterval").value;
    const pasInterval = document.getElementById("pasInterval").value;

    //Si les champs nécessaire ne sont pas vide
    if (borneInferieurInterval !== "" && borneSuperieurInterval !== "" && pasInterval !== "") {

        //Appel du fichier AJAX avec les paramètres passé grace à la méthode POST
        $.post("./ajax/ajoutDonneeVariableViaInterval.ajax.php", {
            borneInferieurInterval: borneInferieurInterval,
            borneSuperieurInterval: borneSuperieurInterval,
            pasInterval: pasInterval
        });
    }
}

//Appel du fichier AJAX afin d'ajouter les nouvelles donnée variable associé au nouveau type de donnée valeur après valeur
function ajouterNouvelleDonneeVariableValeurAValeur() {

//Récupérer les éléments de l'ihm nécessaire
    const liste = [];
    const elementValeurPossible = document.getElementsByClassName("inputDonneeVariable");

    for (let i = 0; i < elementValeurPossible.length; i++) {
        if (!liste.includes(elementValeurPossible[i].value)) {
            liste.push(elementValeurPossible[i].value);
        }
    }

    //Si la liste des donnée variable à ajouter n'est pas vide
    if (liste.length !== 0) {

        //Appel du fichier AJAX avec les paramètres passé grace à la méthode POST
        $.post("./ajax/ajoutDonneeVariableValeurAValeur.ajax.php", {
            liste: liste
        });
    }

}

//Retourne l'id du type de donnée séléctionné
function recupererIdTypeDonneeAjoute() {

    //Récupérer les éléments de l'ihm nécessaire
    let typeDonnee = document.getElementById("selectTypeDonnee");

    //Récupérer la valeur de l'item séléctionné dans la liste déroulante
    typeDonnee = typeDonnee.options[typeDonnee.selectedIndex].value;

    //Retourner l'id du type séléctionné dans la liste déroulante
    return typeDonnee;
}

function recupererIdTypeDonneeAjouteAjaxa(callback) {

    $.ajax({
        type: "POST",
        url: './ajax/recupererIdTypeDonneAjoute.ajax.php',
        dataType: "json",
        success: function (newIdTypeDonneResult) {
            callback(newIdTypeDonneResult);
        }
    });

}


//Retourne le libellé du type de donnée séléctionné
function recupererTypeDonneeAjoute(target) {

    //Récupérer les éléments de l'ihm nécessaire
    const typeDonnee = document.getElementById(target);

    //Récupérer le libellé du type de donnée séléctionné / inséré
    typeDonneeValue = typeDonnee.options[typeDonnee.selectedIndex].value;
    typeDonneeText = typeDonnee.options[typeDonnee.selectedIndex].text;

    //Retourne le type de donné séléctionné
    return [typeDonneeValue, typeDonneeText];
}

//Appel du fichier AJAX afin d'ajouter une nouvelle question dans la base de donnée
function ajouterNouvelleQuestion(libelle) {

    //Si le libellé de la question n'est pas vide
    if (libelle !== "") {
        tableauQuestions.push(libelle);
    }

}

//Retourne un id pour la question/réponse autoincrémenté a chaque fois
function recupererNumQuestionReponseAjax() {

    $.ajax({
        type: "POST",
        url: './ajax/recupererNumQuestionReponse.ajax.php',
        dataType: "json",
        success: function (init_numQRLoc) {
            numQR = init_numQRLoc;
        }
    });

}

//Déclanché si click sur un boutton d'ajout de paramètres
function ajouterParametresCalculeDonnee() {

    if (typeof newId == 'undefined') {
        tableauNumParams.push(0);
        newId = 1;
    } else {
        newId++;
    }

    //Création et paramétrage d'une nouvelle liste déroulante
    const newParam = document.createElement('select');
    newParam.id = "paramCalcul" + newId;
    newParam.classList.add("form-control", "paramCalcul");

    let paramSuplementaires = document.getElementById("paramSuplementaires");

    //Ajout de la nouvelle liste déroulante
    paramSuplementaires.appendChild(newParam);

    populateSelect(tableauDonneeVariable, newParam);

    //Ajout de l'id ajouter au tableau des id
    tableauNumParams.push(newId);
}

//Permet d'ajouter des option à la liste déroulante à partir d'un tableau JSON
function populateSelect(array, newParam) {

    for (let i = 0; i < array.length; i++) {

        //Création des différentes option de la liste déroulante selon le tableau
        const option = document.createElement("option");
        option.value = array[i].getIdType();
        option.text = array[i].getLibelle();

        //Ajout des option à la liste déroulante
        newParam.appendChild(option);
    }
}

//Appeler pour enregistrer les élémennts de calcul de donnée
function validerCalcul() {

    //Récupérer le libellé de la donnée calculée
    const libelleDonneeCalculee = document.getElementById("libelleDonneeCalculee");

    if (libelleDonneeCalculee.value !== "") {

        //Récupérer le nom de la fonction de correction
        let nomFormuleCalcul = document.getElementById("formuleCalcul");
        nomFormuleCalcul = nomFormuleCalcul.options[nomFormuleCalcul.selectedIndex].value;

        //Récupérer les paramètres à passer à la fonction de correction
        const tableauIdParams = Array();
        const listeElementsParams = document.getElementsByClassName('paramCalcul');

        //Pour chaque paramètres
        for (let i = 0; i < listeElementsParams.length; i++) {
            //Récupérer l'id de la donnée variable à utiliser
            let idDonneCalculeeParamsTemp = document.getElementById(listeElementsParams[i].id);
            idDonneCalculeeParamsTemp = idDonneCalculeeParamsTemp.options[idDonneCalculeeParamsTemp.selectedIndex].value;

            //Ajouter cette id au tableau des paramètres de correction de la question
            tableauIdParams.push(idDonneCalculeeParamsTemp);
        }

        ajoutDonneeCalculee(libelleDonneeCalculee.value, nomFormuleCalcul, tableauIdParams);

        return true;
    } else {
        libelleDonneeCalculee.setCustomValidity("Entrez un nom pour la donnée calculée");
        return false;
    }

}

//Appel du fichier AJAX afin d'ajouter une nouvelle DonneeCalculee
function ajoutDonneeCalculee(libelleDonneeCalculee, nomFormuleCalcul, tableauIdParams) {
    //Ajouter le nouveau type de donnée
    $.ajax({
        type: "POST",
        dataType: "json",
        url: './ajax/ajoutTypeDonnee.ajax.php',
        data: {newTypeDonnee: libelleDonneeCalculee},
        success: function () {
            //Ajouter les valeur associé à la nouvelle donnée calculé
            $.ajax({
                type: "POST",
                url: './ajax/ajoutDonneeCalculee.ajax.php',
                dataType: "json",
                data:
                    {
                        libelleDonneeCalculee: libelleDonneeCalculee,
                        nomFormuleCalcul: nomFormuleCalcul,
                        tableauIdParams: tableauIdParams
                    },
                success: function () {
                    refreshSelectTypeDonnee(libelleDonneeCalculee, "selectTypeDonneeCalculee");
                }
            });

        }
    });
}
