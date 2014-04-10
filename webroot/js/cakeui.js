$(function(){
	$(".dropdown-menu > li > a.trigger").on("click",function(e){
		var current=$(this).next();
		var grandparent=$(this).parent().parent();
		if($(this).hasClass('left-caret')||$(this).hasClass('right-caret'))
			$(this).toggleClass('right-caret left-caret');
		grandparent.find('.left-caret').not(this).toggleClass('right-caret left-caret');
		grandparent.find(".sub-menu:visible").not(current).hide();
		current.toggle();
		e.stopPropagation();
	});
	$(".dropdown-menu > li > a:not(.trigger)").on("click",function(){
		var root=$(this).closest('.dropdown');
		root.find('.left-caret').toggleClass('right-caret left-caret');
		root.find('.sub-menu:visible').hide();
	});
	$('.modalButton').click(function(){
	  	$('#modal-content').load($(this).attr('href'),function(result){
		    $('.modalWindow').modal({show:true});
		});
		return false;
	});
	 $(":input").inputmask();
	 $('.cel-inputmask').keyup(function(){
	 	v = $(this).val();
	 	v = v.replace(/\D/g,"");
	 	if(v.length<=10){
	 		changeMask('.cel-inputmask','(99)9999-9999[9]');
	 	} else{
	 		changeMask('.cel-inputmask','(99)9999[9]-9999');
	 	}
	 	return false;
	 });
	 $('.money-inputmask').maskMoney({thousands:'.', decimal:','});
});
function changeMask(field,maskToUse){
	$(field).inputmask("remove");
	$(field).inputmask({"mask":maskToUse});
}