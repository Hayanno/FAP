// SCROLL TO TOP ===============================================================================
$(function() {
	$(window).scroll(function() {
		if($(this).scrollTop() != 0) {
			$('#toTop').fadeIn();	
		} else {
			$('#toTop').fadeOut();
		}
	});
 
	$('#toTop, button.forward, button.backward').click(function() {
		$('body, html').animate({scrollTop:0}, 500);
	});	
	
});

// WIZARD  ===============================================================================
jQuery(function($) {
				//  Basic wizard with validation
				$('form#wrapped').attr('action', 'survey_send_1.php');
				$("#survey_container").wizard({
					stepsWrapper: "#wrapped",
					submit: ".submit",
					beforeSelect: function( event, state ) {
						if ($('input#website').val().length != 0) {
							return false;
						} 
						if (!state.isMovingForward)
  						 return true;
						var inputs = $(this).wizard('state').step.find(':input');
						return !inputs.length || !!inputs.valid();
					}
			

				}).validate({
					errorPlacement: function(error, element) { 
						if ( element.is(':radio') || element.is(':checkbox') ) {
							error.insertBefore( element.next() );

						} else { 
							error.insertAfter( element );
						}
					}
				});
				

				//  progress bar
				$("#progressbar").progressbar();

				$("#survey_container").wizard({
					afterSelect: function( event, state ) {
						$("#progressbar").progressbar("value", state.percentComplete);
						$("#location").text("(" + state.stepsComplete + "/" + state.stepsPossible + ")");
					}
				});

			});

// OHTER ===============================================================================
 $(document).ready(function(){   
    
		//Menu mobile
		$(".btn-responsive-menu").click(function() {
			$("#top-nav").slideToggle(400);
		});
		
		//Check and radio input styles
		$('input.check_radio').iCheck({
    	checkboxClass: 'icheckbox_square-red',
   	    radioClass: 'iradio_square-red'
  		});
		
		//Pace holder
		$('input, textarea').placeholder();
				
		//Carousel
		$("#owl-demo").owlCarousel({
 
		items : 4,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [979,3]
		 
		});
    
    });
/*===================================================================================*/
	/*  TWITTER FEED                                                                     */
	/*===================================================================================*/
	
			$('.latest-tweets').each(function(){
				$(this).tweet({
				username: $(this).data('username'),
				join_text: "auto",
				avatar_size: 0,
				count: $(this).data('number'),
				auto_join_text_default: " we said,",
				auto_join_text_ed: " we",
				auto_join_text_ing: " we were",
				auto_join_text_reply: " we replied to",
				auto_join_text_url: "",
				loading_text: " loading tweets...",
				modpath: "./twitter/"
			});
		});
		
$('.latest-tweets').find('ul').addClass('slider');
		  	if ( $().bxSlider ) {
				var $this = $('.latest-tweets');
				$('.latest-tweets .slider').bxSlider({
					mode 			: 	$this.data('mode') != 'undefined' ? $this.data('mode') : "horizontal",
					speed			:	$this.data('speed') != 'undefined' ? $this.data('speed') : 2000,
					controls		:	$this.data('controls') != 'undefined' != 'undefined' ? $this.data('controls') : true,
					nextSelector 	: 	$this.data('nextselector') != 'undefined' ? $this.data('nextselector') : '',
					prevSelector	: 	$this.data('prevselector') != 'undefined' ? $this.data('prevselector') : '',
					pager			:	$this.data('pager') != 'undefined' ? $this.data('pager') : true,
					pagerSelector	: 	$this.data('pagerselector') != 'undefined' ? $this.data('pagerselector') : '',
					pagerCustom		: 	$this.data('pagercustom') != 'undefined' ? $this.data('pagercustom') : '',
					auto			:	$this.data('auto') != 'undefined' ? $this.data('auto') : true,
					autoHover		: 	$this.data('autoHover') != 'undefined' ? $this.data('autoHover') : true,
					adaptiveHeight	: 	$this.data('adaptiveheight') != 'undefined' ? $this.data('adaptiveheight') : true,
					useCSS			: 	$this.data('useCSS') != 'undefined' ? $this.data('useCSS') : false,
					nextText		: 	'<i class="icon-angle-right">',
					prevText		: 	'<i class="icon-angle-left">',
					preloadImages 	: 	'all',
					responsive 		: 	true
				});
			}

// AJOUTS ===============================================================================

var parseQueryString = function() {

    var str = window.location.search;
    var objURL = {};

    str.replace(
        new RegExp( "([^?=&]+)(=([^&]*))?", "g" ),
        function( $0, $1, $2, $3 ){
            objURL[ $1 ] = $3;
        }
    );
    return objURL;
};

jQuery(function($) {
	
	var params = parseQueryString();
	
	if(params['type'] == "two") {
		$("#basic-info").append('<li><input type="email" name="mail" class="required form-control" placeholder="Votre mail"></li>');
		$("#basic-info-partenaire").append('<li><input type="email" name="mail_partenaire" class="required form-control" placeholder="Mail de votre partenaire"></li>');
		$("#type").val("two");
	}
	
	if("clef" in params){
		$.ajax({ 
			type: 'POST', 
			url: 'getQuestionnaire.php',
			data: 'clef=' + params["clef"],
			success: function (data) {
				data = JSON.parse(data);
				
				$("#pseudo").val(data.utilisateur_2.pseudo).blur();
				$("#pseudo_partenaire").val(data.utilisateur_1.pseudo).blur();
				
				// v  pas beau et à refaire avec le vrai "sexe"
				$("#ul-sexe input:eq(" + (data.utilisateur_2.sexe_id - 1) + ")").iCheck('check');
				$("#ul-sexe-partenaire input:eq(" + (data.utilisateur_1.sexe_id - 1) + ")").iCheck('check');
			}
		});
		
		$("#intro input:not([name=terms])").prop('disabled', true);
		
		//modifier input ici
	}
	
	$.ajax({ 
		type: 'GET', 
		url: 'questions.json',
		dataType: 'json',
		success: function (data) {
			data = data.questions;
			
			$.each(data, function(index, element) {
				$('#cat-' + element.cat).append("\
				<div class=\"row \">" +
					"<div class=\"col-md-4 question\">" +
						element.texte +
					"</div>" +
					"<div class=\"col-md-8 reponse\">" +
						"<ul class=\"data-list floated clearfix\">" +
							"<li><input name=\"question_" + index + "\" type=\"radio\" class=\"required check_radio\" value=\"1\"><label class=\"enligne-non\">Non</label></li>" +
							"<li><input name=\"question_" + index + "\" type=\"radio\" class=\"required check_radio\" value=\"2\"><label class=\"enligne-pratique\">Nous pratiquons déjà cela</label></li>" +
							"<li><input name=\"question_" + index + "\" type=\"radio\" class=\"required check_radio\" value=\"3\"><label class=\"enligne-interesse\">Si mon partenaire est intéressé</label></li>" +
							"<li><input name=\"question_" + index + "\" type=\"radio\" class=\"required check_radio\" value=\"4\"><label class=\"enligne-oui\">Oui !!</label></li>" +
						"</ul>" +
					"</div>" +
				"</div>");
			});
			
			$('#nbrquestion').val(data.length);
			
			//Check and radio input styles
			$('input.check_radio').iCheck({
				checkboxClass: 'icheckbox_square-aero',
				radioClass: 'iradio_square-aero'
			});
			
			$("#pseudo").blur();
			$("#pseudo_partenaire").blur();
		}
	});
	
	$("#pseudo").blur(function() {
		$(".utilisateur").html("<strong>" + $("#pseudo").val() + "</strong>");
	});
	$("#pseudo_partenaire").blur(function() {
		$(".utilisateur_partenaire").html("<strong>" + $("#pseudo_partenaire").val() + "</strong>");
	});
});





    

