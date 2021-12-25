//EXPAND DASHBOARD
$(document).ready( function(){
	$('.closeside').click( function() {
		var toggleWidth = $("#sidebar2").width() == 0 ? "20%" : "0%";
			$('#sidebar2').animate({ width: toggleWidth });
			$('#mainbar').toggleClass('w-full');
			$('.closeside').toggleClass('left0');
			$('.leftchevronlds, .rightchevronlds').toggleClass('hidethis_svg');
		});
});

//EXPAND GRID CARD
function toggleGridCard(divId){
$('#gridcard'+divId).attr('data-gs-height', function(index, attr){
	return attr == 4 ? 1 : 4;
});
}
