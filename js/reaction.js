/* create :shirase    */
/* website:fsbblog.jp */
/* date   :2021/09/26 */
function iine_reaction(){
 $.post( '/iine.php', $('#iine_form').serialize());
 $("#iine_count").html("ThankYou!");
 $("#iine_button").prop("disabled", true);
}