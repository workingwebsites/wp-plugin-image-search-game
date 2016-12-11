// JavaScript Document

jQuery( document ).ready(function() {
	ShowHints(parseInt(3000))
	
	jQuery(function () {
		jQuery('.pannable-image').ImageViewer();
	});
});

function ShowHints(BaseTime) {
//Displays Hints at time entered
	setTimeout(function(){jQuery("#alf_Hint1").show('slow'); }, BaseTime);
	setTimeout(function(){jQuery("#alf_Hint2").show('slow'); }, parseInt(BaseTime*2));
	setTimeout(function(){jQuery("#alf_Hint3").show('slow'); }, parseInt(BaseTime*3));
	setTimeout(function(){jQuery("#alf_Hint4").show('slow'); }, parseInt(BaseTime*4));

}

