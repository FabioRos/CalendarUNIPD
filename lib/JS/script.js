jQuery(document).ready(function() {
     
     $('#lista').click(function() {
     	showList();
     });
     
     $('#tabella').click(function() {
     	showTable();
     });
     
     
    //LABORATORI
    $('#lab_ref').click(function(event) {
        $('#formDatePicker').show();
        
        //cancello tutto ad eccezione di quella delle ore:
        nascondi();
        $('td:nth-child(1),th:nth-child(1)').show();
        
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
    
        $('#formDatePicker').show();
	//cancello tutto ad eccezione di quella delle ore:
        nascondi();
        $('td:nth-child(1),th:nth-child(1)').show();
        
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
    
      //LUZZATI
    $('#luzzatti_ref').click(function(event) {
        $('#formDatePicker').show();
	//cancello tutto ad eccezione di quella delle ore:
        nascondi(); 
        $('td:nth-child(1),th:nth-child(1)').show();
        
        //ora mostro  solo le aule del paolotti
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
    $('#torre_ref').click(function(event) {
        $('#formDatePicker').show();
	//cancello tutto ad eccezione di quella delle ore:
        nascondi(); 
        $('td:nth-child(1),th:nth-child(1)').show();
        
        //ora mostro  solo i laboratori
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
    	
    
    //TUTTE
    $('#tutte_ref').click(function(event) {
       // nascondo solo la descrizione
        $('.descrizione').hide();
         event.preventDefault();
    });
    
    
});

function nascondi()
{
	$('.descrizione').hide();
    $('#tblPrenotazioni').find('th').hide();
    $('#tblPrenotazioni').find('td').hide();
}

function showList()
{
	$('#tblWrapper').hide();
	$('#listWrapper').show();
}

function showTable()
{
	$('#listWrapper').hide();
	$('#tblWrapper').show();
}
