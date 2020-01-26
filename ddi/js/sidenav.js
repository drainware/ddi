$(document).ready(function(){
	$("#sidebutton").click(function(){
		if($("#sidenav").css("display")=="none"){
			$("#sidenav").show(800);
			$("#sidebutton img").css({"transform":"rotate(180deg)","-webkit-transform":"rotate(180deg)"});
			$(this).css("left","123px");
			$("#main_content").css("margin-left","123px");
		}
		else{
			$("#sidenav").hide(600);
			$("#sidebutton img").css({"transform":"rotate(0deg)","-webkit-transform":"rotate(0deg)"});
			$(this).css("left","0px");
			$("#main_content").css("margin-left","0px");
		}
	})
});
