/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Formulaire ajout validator
try {
    var $validator = jQuery("#formAjout").validate({
        lang: 'fr',
        highlight: function (formElement, label) {
            jQuery(label).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        success: function (label, formElement) {
            jQuery(label).closest('.form-group').removeClass('has-error');
            $(label).remove();
        }
    });
} catch (e) {
    console.log("Erreur Validator", e);
}


/**
 * Creation ou modification
 * 
 * @param string url Lien
 * @param object $formObject
 * @param string formData données serializées à envoyer
 * @param object $ajoutLoader
 * @param object $table L'objet bootstrap-table
 * @param boolean ajout determine si c'est un ajout ou une modification
 * @returns null
 */
function editerAction(url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
    jQuery.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: formData,
        success: function (reponse, textStatus, xhr) {
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $(".bs-modal-ajout").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
            }
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png',
            });
        },
        error: function (err) {
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: "Cet enregistrement existe dans la base",
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png'
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
        beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        }
    });
}

/**
 * Creation ou modification avec envoi d'image
 * 
 * @param string url Lien
 * @param object $formObject
 * @param string formData données serializées à envoyer
 * @param object $ajoutLoader
 * @param object $table L'objet bootstrap-table
 * @param boolean ajout determine si c'est un ajout ou une modification
 * @returns null
 */
function editerImageAction(url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
    jQuery.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success: function (reponse) {
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                }
                $(".bs-modal-ajout").modal("hide");
            }
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png',
            });
        },
        error: function (err) {
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: "Erreur survenue, réessayer plus tard",
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png'
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
        beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        }
    });
}

function facturerAction(url, $formObject, formData, $ajoutLoader, venteArticle = true) {
    jQuery.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: formData,
        success: function (reponse, textStatus, xhr) {
            if (reponse.code === 1) {
//                if (venteArticle) { //Impression reçu
//                    
//                } else { //Impression reçu
//                    
//                }

                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.populateFacture(reponse.data);
                    if (reponse.data.reglements) {
                        $scope.populateReglement(reponse.data.reglements[0]);
                    }
                });
                $formObject.trigger('eventFacturer', [reponse.data]);
            }
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png'
            });
        },
        error: function (err) {
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: "Erreur survenue, réessayer plus tard",
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png'
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
        beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        },
        statusCode: {
            302: function () {
                $.gritter.add({
                    // heading of the notification
                    title: "SOCOCI",
                    // the text inside the notification
                    text: "Votre session a expiré. Veuillez vous reconnecter.",
                    sticky: false,
                    image: baseUrl + 'img/gritter/confirm.png'
                });
            }
        }
    });
}


//Supprimer un enregistrement
function supprimerAction(url, formData, $question, $ajaxLoader, $table) {
    jQuery.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: formData,
        success: function (reponse) {
            if (reponse.code === 1) {
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [reponse.data.id]
                });
                $(".bs-modal-suppression").modal("hide");
            }
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png',
            });
        },
        error: function (err) {
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: "Erreur survenue, réessayer plus tard",
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png'
            });
            $ajaxLoader.hide();
            $question.show();
        },
        beforeSend: function () {
            $question.hide();
            $ajaxLoader.show();
        },
        complete: function () {
            $ajaxLoader.hide();
            $question.show();
        }
    });
}
//Changement etat du compte
function compteAction(url, formData, $question, $ajaxLoader, $table) {
    jQuery.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: formData,
        success: function (reponse) {
            if (reponse.code === 1) {
                $table.bootstrapTable('updateByUniqueId', {
                    id: reponse.data.id,
                    row: reponse.data
                });
                $(".bs-modal-compte").modal("hide");
            }
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png',
            });
        },
        error: function (err) {
            $.gritter.add({
                // heading of the notification
                title: "SOCOCI",
                // the text inside the notification
                text: "Erreur survenue, réessayer plus tard",
                sticky: false,
                image: baseUrl + 'img/gritter/confirm.png'
            });
            $ajaxLoader.hide();
            $question.show();
        },
        beforeSend: function () {
            $question.hide();
            $ajaxLoader.show();
        },
        complete: function () {
            $ajaxLoader.hide();
            $question.show();
        }
    });
}


//Select2 ajax formatters
function formatWorkStaff(workStaff) {
     if (workStaff.depart == true) {
        return "Cet employé n'existe plus dans la base";
    }else{
        return workStaff.fullName + "<span class='pull-right'>" + (workStaff.post) + "</span>";
    }
//    if (workStaff.loading) {
//        return workStaff.fullName;
//    }

//    return  workStaff.fullName + "<span class='pull-right'>" + (workStaff.phone1 ? workStaff.phone2 : '') + "</span>";
//    return  workStaff.fullName + "<span class='pull-right'>" + (workStaff.post) + "</span>";
}

function formatWorkStaffSelection(workStaff) { //TODO
   
    return workStaff.fullName || workStaff.text;
}

