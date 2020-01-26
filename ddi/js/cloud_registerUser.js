/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$("#personal_cloud_user_opt").click(function() {
    $("#options_company_cloud_user").hide();
    $("#company_cloud_user").removeClass("required");
    $("#cif_cloud_user").removeClass("required");
});

$("#company_cloud_user_opt").click(function() {
    $("#options_company_cloud_user").show();
    $("#company_cloud_user").addClass("required");
    $("#cif_cloud_user").addClass("required");
});