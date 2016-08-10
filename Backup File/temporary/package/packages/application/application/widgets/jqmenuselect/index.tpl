<script  src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
<script type="text/javascript">
var jqcc = $.noConflict();
jqcc(document).ready(function(e) {
		var bodyID = jqcc('body').attr('id').valueOf();
		var explodedID = bodyID.split('-');
		var globalPageID = explodedID[0];
		var globalPageIDExploded = globalPageID.split('_');
		//console.log(globalPageIDExploded[2]);
		//selecting each element
		var eachLink = jqcc('ul.navigation li').children('a').each(function(){
				var eachLinkClass = jqcc(this).attr('class').valueOf();
				//console.log(eachLinkClass);
				var eachLinkClassSeperated = eachLinkClass.split(" ")
				//console.log(eachLinkClassSeperated[1]);
				var eachLinkClassExploded = eachLinkClassSeperated[1].split("_")
				//console.log(eachLinkClassExploded[2]);
				if (bodyID == "global_page_user-index-home"){
						jqcc('.core_main_home').parent().addClass('active');
				}else if(eachLinkClassExploded[2].toLowerCase() == globalPageIDExploded[2].toLowerCase()){
					
						if(!jqcc(this).parent().hasClass('active')){
							jqcc(this).parent().addClass('active')
						}
				
					}
			})
		
});

</script>
