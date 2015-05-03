jQuery(document).ready(function() {
     showTable(); // default
     //$('menuHomePage').addClass("selected");
    
     $('#lista').click(function() {
     	showList();
     });
     
     $('#tabella').click(function() {
     	showTable();
     });
     
    //LABORATORI
    $('#lab_ref').click(function(event) {
    	//aggiunta classe css selected
       	togli_classi_selezione_mainmenu();
    	$(this).parent('li').addClass("selected");
    	 clickedMenuItem();
        //ora mostro  solo i laboratori
        $('#tblPrenotazioni').find('th').each(function($index) {
            if ($(this).html() == ' LAB TA ' || 
                $(this).html() == ' LAB P036 ' || 
                $(this).html() == ' LAB P140 ') {   //alert($index); //$index contiene il numero della cella
                var $aux = $index + 1;
                $('td:nth-child(' + $aux + '),th:nth-child(' + $aux + ')').show();
            }
        });
        event.preventDefault();
    });
    
    //PAOLOTTI
    $('#paolotti_ref').click(function(event) {
    	//aggiunta classe css selected
       	togli_classi_selezione_mainmenu();
    	$(this).parent('li').addClass("selected");
    	clickedMenuItem();
        //ora mostro  solo le aule del paolotti
        $('#tblPrenotazioni').find('th').each(function($index) {
            if ($(this).html() == ' P1 ' || 
                $(this).html() == ' P2 ' || 
                $(this).html() == ' P3 ' || 
                $(this).html() == ' P4 ' || 
                $(this).html() == ' P5 ' || 
                $(this).html() == ' P6 ' || 
                $(this).html() == ' P200 ' ||
		$(this).html() == ' P300 ' ||
                $(this).html() == ' LAB P036 ' || 
                $(this).html() == ' LAB P140 ') {
                var $aux = $index + 1;
                $('td:nth-child(' + $aux + '),th:nth-child(' + $aux + ')').show();
            }
        });
        event.preventDefault();
    });
    
      //LUZZATTI
    $('#luzzatti_ref').parent('li').click(function(event) {
    	//aggiunta classe css selected
       	togli_classi_selezione_mainmenu();
    	$(this).addClass("selected");
       	 clickedMenuItem();
        //ora mostro  solo le aule di via luzzatti
        $('#tblPrenotazioni').find('th').each(function($index) {
            if ($(this).html() == ' LUM250 ' || 
                $(this).html() == ' LUF1 ' ||
		$(this).html() == ' LU3 ' ||
		$(this).html() == ' LU4 ' ) {
                var $aux = $index + 1;
                $('td:nth-child(' + $aux + '),th:nth-child(' + $aux + ')').show();
            }
        });
        event.preventDefault();
    });
    

	//TORRE
    $('#torre_ref').parent('li').click(function(event) {
       	 clickedMenuItem();
       	 //aggiunta classe css selected
       	togli_classi_selezione_mainmenu();
    	$(this).addClass("selected");
        //ora mostro  solo le aule della torre
        $('#tblPrenotazioni').find('th').each(function($index) {
            if ($(this).html() == ' LAB TA ' || 
            $(this).html() == ' 1BC45 ' ||
			$(this).html() == ' 1BC50 ' ||
			$(this).html() == ' 1A150 ' ||
			$(this).html() == ' 1C150 ' ||
			$(this).html() == ' 1AD100 ' ||
			$(this).html() == ' 2BC60 ' ||
			$(this).html() == ' 2AB40 ' || 
			$(this).html() == ' 2AB45 ' || 
			$(this).html() == ' 2BC30 ' || 
			$(this).html() == ' SRVII ' || 
            $(this).html() == ' SRVI ') {   //alert($index); //$index contiene il numero della cella
                var $aux = $index + 1;
                $('td:nth-child(' + $aux + '),th:nth-child(' + $aux + ')').show();
            }
        });
        event.preventDefault();
    });
    	
    	
   	$('menuHomePage').click(function(event) {
       //aggiunta classe css selezionato
       	togli_classi_selezione_mainmenu();
    	$(this).parent('li').addClass("selected");
      	mostra_tutto();
        event.preventDefault();
    });
    
    //TUTTE
    $('#tutte_ref').click(function(event) {
       //aggiunta classe css selezionato
       	togli_classi_selezione_mainmenu();
    	$(this).parent('li').addClass("selected");
       
       mostra_tutto();
       // nascondo solo la descrizione
        $('.descrizione').hide();
        event.preventDefault();
    });
    
    // AULE LIBERE
    $('#libere_ref').click(function(event) {
       //aggiunta classe css selezionato
       	togli_classi_selezione_mainmenu();
    	$(this).parent('li').addClass("selected");
    	
    	showEmptyRooms();
       
        event.preventDefault();
    });
    
});

function togli_classi_selezione_mainmenu(){
	$('#mainmenu').find('li').each(function(){
		$(this).removeClass('selected');
	});
}

/* Ripristina il toogle e tutti gli elementi della tabella. */
function mostra_tutto() {
	  $('#toogleWrapper').show(); // ripristino il toogle
      $('#tblPrenotazioni').find('th').each(function($index) {
       	var $aux = $index + 1;
        $('td:nth-child(' + $aux + '),th:nth-child(' + $aux + ')').show();
      });
}

/* Nasconde la descrizione, il toogle, la lista delle aule libere, tutti gli elementi della tabella. */
function nascondi() {
	$('.descrizione').hide();
	$('#toogleWrapper').hide(); // nascondo il toogle
    $('#tblPrenotazioni').find('th').hide();
    $('#tblPrenotazioni').find('td').hide();
    $('#listWrapperEmptyRooms').hide(); // nascondo le aule libere
}

/* Ripristina la tabella, richiama la funzione nascondi */
function clickedMenuItem() {
	showTable(); // ripristino la tabella
    $('#formDatePicker').show();
      
    //cancello tutto ad eccezione di quella delle ore:
    nascondi();
    $('td:nth-child(1),th:nth-child(1)').show();
}

function showList() {
	$('#tblWrapper').hide();
	$('#listWrapper').show();
	$('#listWrapperEmptyRooms').hide(); // nascondo le aule libere
}

function showTable() {
	$('#listWrapper').hide();
	$('#tblWrapper').show();
	$('#listWrapperEmptyRooms').hide(); // nascondo le aule libere
}

function showEmptyRooms() {
	$('.descrizione').hide();
	$('#tblWrapper').hide();
	$('#listWrapper').hide();
	$('#toogleWrapper').hide();
	$('#listWrapperEmptyRooms').show(); // mostro le aule libere
}
