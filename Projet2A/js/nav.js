let text = document.getElementsByClassName("div-nav-right-deco")[0]; // Ici on prend tous les éléments qui ont la classe "change" et on récupere le 0, donc le premier qui a cette classe et on créé une variable "text" où on stock cette balise
let oldContent = "";  // Ici c'est une variable, elle va nous permettre de garder le contenu de la balise

text.addEventListener('mouseenter', function(){
  /*
  text => On prend la balise définie au-dessus
  
  .addEventListener => On lui défini un "événement", donc quelques choses qui va se passer quand on fera X action
  
  'mouseenter' => On dis que l'action doit se déclencher quand le curseur rentre (mouse = Souris ET enter = entrer, donc quand la souris entre dans l'élément)
  
  'function(){}' => Une fonction, donc l'action qui doit être faite quand le curseur est sur l'élément "text"

  En bref : Quand la souris rentre dans la balise "text", alors on fait l'action contenue dans la fonction: function(){}
  */
  
  oldContent = text.innerText; //On stocke le contenu de la balise dans "oldContent".
  text.innerText = "Déconnexion"; // On remplace le texte de la balise avec "Déconnexion".
  text.classList.add('changeHover'); // Et enfin, on met la classe "changeHover" à la balise.
  
})

text.addEventListener('mouseleave', ()=>{
  
  /*
  text => On prend la balise définie au-dessus
  
  .addEventListener => On lui défini un "événement", donc quelques choses qui va se passer quand on fera X action
  
  'mouseleave' => On dis que l'action doit se déclencher quand le curseur sort (mouse = Souris ET leave = sort, donc quand la souris sort de l'élément)
  
  '()=>{}' => C'est STRICTEMENT la même chose que "function(){}"  c'est juste une manière plus compressée de l'écrire !
  
  En bref : Quand la souris sort de la balise "text", alors on fait l'action contenue dans la fonction: ()=>{}
  */
  
  text.innerText = oldContent; //On remet l'ancien contenu dans la balise "text"
  text.classList.remove('changeHover'); // On lui retire la classe qu'on lui avait mis avant
})