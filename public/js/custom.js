// $(document).ready(function(){
//     // Hide displayed paragraphs
//     $(".own-heading").click(function(){
//         $(".add_wrapper").hide();
//     });
    
//     // Show hidden paragraphs
//     $(".own-heading").click(function(){
//         $(".add_wrapper").show();
//     });
// });


// $(document).ready(function(){
	/*$(".own-heading").click(function(){
    	$(".add_wrapper").removeAttr("style");
        $(".add_wrapper").toggleClass("anim1");		
    });*/
// });


const targetDiv = document.getElementById("sidebar");
const btn = document.getElementById("toggle_btn");
btn.onclick = function () {
  if (targetDiv.style.display !== "none") {
    targetDiv.style.display = "none";
  } else {
    targetDiv.style.display = "block";
  }
};

