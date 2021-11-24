<!-- Modal ajout et modification -->
<div class="modal fade"  id="ModalImportationEnMasse" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <form id="formImportationEnMasse" action="{{$action}}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-download"></i>
                        Importation en masse
                    </span>
                </div>
                <div class="modal-body ">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Choisissez le fichier *</label>
                                <div class="input-group fileToUpload">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input name="fileToUpload" type="file" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="uploadFileFromAjax('formImportationEnMasse')" class="btn btn-primary"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    /*
    |--------------------------------------------------------------------------
    | SCRIPT CREATION EN MASSE
    |--------------------------------------------------------------------------
    */
    function uploadFileFromAjax(formId,dataTableId=null){
        $.ajax({
            type: 'POST',
            url: $("#"+formId).attr('action'),
            data: new FormData(document.getElementById(formId)),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                //
            },
            success: function(data){
                if(data.isFile){
                    exportAsExcelFile(data.data,data.filename)
                }
                // $('.modal').modal('hide');
                // if(data.message!=undefined){
                //     // Create an instance of Notyf
                //     var notyf = new Notyf({position: {x: 'right',y: 'top'}});
                //     // Display a success notification
                //     notyf.success(data.message);
                // }

                // $("#"+formId).trigger("reset");
                // $('.modal').modal('hide');
                //Raffraichissement du tableau
                if(dataTableId!=null){
                    $('#'+dataTableId).DataTable().ajax.reload();
                }
            },
            error: function(xhr){
                console.log('error', xhr)
                // if(xhr.responseJSON.message!=undefined){
                //     swal({
                //         title: 'Echec...',
                //         text: xhr.responseJSON.message,
                //         type: "error",
                //         showConfirmButton: true,
                //     })
                // }
            }

        });
    }
</script>

<script>
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
    const EXCEL_EXTENSION = '.xlsx';
    function exportAsExcelFile(jsonData, excelFileName) {
        const worksheet=XLSX.WorkSheet = XLSX.utils.json_to_sheet(jsonData);
        const workbook=XLSX.WorkBook = { Sheets: { 'data': worksheet }, SheetNames: ['data'] };
        const excelBuffer= XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
        console.log('Buffer:'+excelBuffer,worksheet,workbook)
        this.saveAsExcelFile(excelBuffer, excelFileName);
    }
    function saveAsExcelFile(buffer,fileName) {
        const data= new Blob([buffer], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8'});
        // saveAs(data, fileName + '_export_' + new  Date().getTime()+'.xlsx');
        //Télécharger sur tous les navigateur
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(data);
        link.download = fileName +'-'+ new  Date();
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    // $("input[name='fileToUpload']").on("change", function (e) {
    //     var file = e.target.files[0];
    //     // input canceled, return
    //     if (!file) return;

    //     var FR = new FileReader();
    //     FR.onload = function(e) {
    //         var data = new Uint8Array(e.target.result);
    //         var workbook = XLSX.read(data, {type: 'array'});
    //         var firstSheet = workbook.Sheets[workbook.SheetNames[0]];

    //         // header: 1 instructs xlsx to create an 'array of arrays'
    //         var result = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
    //         console.log('output', result)

    //         // data preview
    //         var output = document.getElementById('result');
    //         output.innerHTML = JSON.stringify(result, null, 2);
    //     };
    //     FR.readAsArrayBuffer(file);
    // });
</script>

