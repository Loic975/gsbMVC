function afficherStatVisiteur() 
{
	// Permet l‘exécution d'une fonction spécifique une fois le document DOM
      // entièrement   chargé, et prêt à être parcouru et manipulé.
	$(document).ready(function(){
		$.post(
			'ajax/_indexAjax.php',  //  URL script à exécuter
			{
				// données envoyées
				uc : 'statVisiteur',
				action : 'voirStatVisiteur',			
				lstVisiteur : $("#lstVisiteur").val(),  
			},
			// fonction pour gérer le retour
			function(data){
                                                      $("#zoneStat").html(data);   
				},
			'text'     // format de retour, texte HTML donc on indique text 
			 );
		});
}
