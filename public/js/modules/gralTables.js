$( document ).ready(function() {

	$('#dataTable').dataTable( {
		"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap",
		"bDestroy": true,
		"bLengthChange": false,
		"bPaginate": true,
		"bFilter": true,
		"bSort": true,
		"bJQueryUI": true,
		"iDisplayLength": 10,      
		"bProcessing": true,
		"bAutoWidth": true,
		"bSortClasses": false,
	      "oLanguage": {
	          "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
	          "sEmptyTable": "Sin registros.",
	          "sInfoEmpty" : "Sin registros.",
	          "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
	          "sLoadingRecords": "Leyendo información",
	          "sProcessing": "Procesando",
	          "sSearch": "Buscar:",
	          "sZeroRecords": "Sin registros",
	          "oPaginate": {
	            "sPrevious": "Anterior",
	            "sNext": "Siguiente"
	          }          
	      }
	} );
});	

function editDataTable(idTableRow){
	var editTableSrc = $("#hRefLinkEdit").val()+idTableRow;
	$('#iFrameModalAdd').attr('src',editTableSrc);
	$("#modalTableEdit").modal('show');
    $('#modalTableEdit').on('hidden.bs.modal', function () {
        location.reload();
    });   
}

function confirmDelete(idtableRow){	
	$("#modalConfirmDelete").modal('show');
}