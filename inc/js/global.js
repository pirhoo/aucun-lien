
/**
 * @name Site
 * @constructor 
 */
Site = function($) {	
	var site = this;		

	// when the dom is ready
	$(document).ready(function() { site.ready.call(site) });
};


/**
 * @name ready
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.ready = function() {	


	// montre le formulaire de connexion
	$(".to-login").click(function() {
		$(".state").addClass("hidden").filter(".state-login").removeClass("hidden");
	});

	// cache le formulaire de connexion
	$(".to-logout").click(function() {		
		$(".state").addClass("hidden").filter(".state-logout").removeClass("hidden");
	}); 

	// cache la popup
	$(".js-close-fg").on("click", function() {
		$(".js-overlay").fadeOut(600);
		$(".js-popup").animate({"top":"-100%"}, 700, function() { $(".js-fg-group").remove() });		
	});
  
  // Scan les liens de partages
  this.scanShare();
    
	// Si filtres il doit y avoir
	if( ! $("header").hasClass("remove-filters") ) {

		// initialize les flux
		this.initFlux();

		// change l'état des switchers
		$('.switcher').click(this.toggleSwitcher);

		// dessine l'horloge correctement
		//this.drawClock();
		
		// this.drawDate();
			
		// ouverture et fermeture des filtres
		// $(".filters .open, .filters h2").click(this.toggleFilters);

		// lorsque un élément du formulaire de filtre change 
		$("#filters").change(this.fluxFilters.change);	
		$(".all-tags, .all-moods", "#filters").change(this.fluxFilters.allOptions);		

		/*/
		// Pour changer d'heure lorsque l'on click sur un item du menu
		$(".picker.interval li").live("click", function() {
			// Heure de départ de l'interval
			var $this = $(this),
				time = $this.data("time"),		
				word = $this.data("word");		

			// Mise à jour de l'heure
			$(".time .clock").data("time", time);

			// Mise à jour de l'horloge
			window.site.drawClock();

			// Active le filtre
			$this.parents(".span-6").find(":input[type=checkbox]").prop("checked", true).trigger("change");
		});

		// Pour changer la date lorsque l'on click sur un item du menu
		$(".picker.month li, .picker.year li").live("click", function() {
			
			var $this 	 = $(this),
				$picker  = $this.parents(".picker"),
				$current = $picker.find(".current"),
				$display = $picker.parents(".val").find(".display");		
			
			// Si on selectionne le mois
			if( $picker.hasClass("month") ) {
				// Enregistre le mois en cours
				$(".date .values").data("month", $this.data("month") );
			// Si on selectionne l'année
			} else {
				// Enregistre le mois en cours
				$(".date .values").data("year", $this.data("year") );							
			}

			// Mise à jour de la date
			window.site.drawDate();

			// Active le filtre
			$this.parents(".span-6").find(":input[type=checkbox]").prop("checked", true).trigger("change");
			
		});

		// Activation/desactivation de l'heure
		$(":input[name=filter-time],:input[name=filter-date]").change(function() {		
			var $this   = $(this),
				$parent = $this.parents(".span-6");

			if( $(this).is(":checked") )
				$parent.removeClass("disabled");
			else
				$parent.addClass("disabled");

			// Met à jour le flux
			window.site.fluxFilters.change();
			
		});
		/*/

	}

	
		// vérification du formulaire d'inscription avant soumission
    $("#sign-up").bind("submit change", function(event) {
        if(! window.site.signup_checkField() ) {
        	event.preventDefault();
        }
    });	
	
		// vérification du formulaire de mise à jour du profil avant soumission
    $("#user-update-data").bind("submit change", function(event) {
        if(! window.site.user_update_checkField() ) {
        	event.preventDefault();
        }
    });	

    // Changement de page sans rechargement.
    $("#container a:not(.load), header a:not(.load)").bind("click", function(event) {	
    	
    	var url = $(this).attr("href") || "";

		// Si l'object history admet les pushState et si le lien pointe vers le domain.
		if( Modernizr.history && url.indexOf(window.location.hostname) > - 1 ) {
			// Bloque l'ouverture du lien.
			event.preventDefault();
			// Charge la page.
			window.site.loadPage( url );
		}	
    });

    // Change dans l'historique.
	$(window).bind("popstate", function (event) { 
		
		// Chrome déclenche un popstate au chargement
		// On attend qu'au moins une page soit loadée
		// @src http://stackoverflow.com/questions/6421769/popstate-on-pages-load-in-chrome
		if (!window.history.ready && !event.originalEvent.state) return;
		
		// Charge la page cible sans tout recharger.
		window.site.loadPage(event.target.location.href); 

	});	

	// Pour supprimer les .content superflux
	$("#main .content").live(
		[
			"transitionend",
			"animationend",
			"oTransitionEnd",
			"oAnimationEnd",
			"webkitTransitionEnd",
			"webkitAnimationEnd",
			"msTransitionEnd",
			"msAnimationEnd"
		].join(" "), function() {
		// supprime !
		$("#main .content.remove").remove();
	});

	// Pour mettre un tweet en favoris
	$("a.bw-icon.bookmark").live("click", function() {
		window.site.toggleBookmark.call(this);
	});	
};



/**
 * @name initFlux
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.initFlux = function() {
	
	// calcule la largeur des wrappers
	this.wrappersWidth();

	// récupère l'instance de jscrollpan si il y en a une
	$('.flux').each(function() {

		var jsp = $(this).data("jsp") || undefined;

		// si elle existe on ne fait que reinitialiser le panel
		if(jsp) {						
			// place les scrollbar sur les flux
			if( $(this).find(".page").length > 3 )
				jsp.reinitialise();
			else {		
				jsp.destroy();		
			}
		// sinon on en fait une nouvelle
		} else {
			// place les scrollbar sur les flux
			$(this).jScrollPane({
				hideFocus: true,
				animateScroll:true,
				horizontalDragMinWidth: 110,
				horizontalDragMaxWidth: 110
			});
		}			

	});

	// adapte la taille des carractères
	this.adaptTweetFontSize();

	// bind l'évènement scroll horizontal
	$(".flux").unbind("jsp-scroll-x").bind("jsp-scroll-x", this.scrollFlux);
	

};

/**
 * @name fluxFilters
 * @memberOf Site 
 * @public
 */
Site.prototype.fluxFilters = {
	
	/**
	 * @name change
	 * @memberOf Site.fluxFilters
	 * @function
	 * @public
	 */
	change: function(event) {
			
		var $form   = $("#filters"),
			$navigation = $(".navigation"),
			list    = -1,
			tags    = [],
			moods   = [],
			date = -1,
			time = -1;

		// determine la liste à utiliser
		list = $form.find("[name=list]:checked").val();	
		// Active le label 
		$form.find("[name=list]").parents("label").removeClass("active");
		$form.find("[name=list]:checked").parents("label").addClass("active");

		var isAllMoods =  true;
		// collecte les moods à utiliser
		$form.find("[name=mood]").each(function() {		
			// si la case est checkée	
			if( $(this).is(":checked") && $(this).val() > -1 ) {
				// ajoute la valeur au tableau
				moods.push( $(this).val() );

			} else if( $(this).val() > -1 ) {
				isAllMoods = false;
			}

			$(this).parents("label").toggleClass("active", $(this).is(":checked") );
		});

		// Selection de tous les moods (tableau vide)
		if( event && $(event.target).hasClass("all-moods") && $(event.target).is(":checked") || isAllMoods) moods = []; 
		// Ou deselectionne le "all" bouton si on vient de selectionner un mood
		else if( event && $(event.target).is("[name=mood]") ) $form.find(".all-moods").prop("checked", false).prop("disabled", false).parents("label").removeClass("active");


		/*/
		var isAllTags =  true;
		// collecte les tags	
		$form.find("[name=tag]").each(function() {
			// si la case est checkée	
			if( $(this).is(":checked") && $(this).val() > -1 ) {
				// ajoute la valeur au tableau
				tags.push( $(this).val() );
			
			} else if( $(this).val() > -1 ) {
				isAllTags = false;
			}
			
			$(this).parents("label").toggleClass("active", $(this).is(":checked") );
		});

		// Selection de tous les tags (tableau vide)
		if( event && $(event.target).hasClass("all-tags") && $(event.target).is(":checked") || isAllTags ) tags = []; 
		// Ou deselectionne le "all" bouton si on vient de selectionner un tag
		else if( event && $(event.target).is("[name=tag]") ) $form.find(".all-tags").prop("checked", false).prop("disabled", false).parents("label").removeClass("active");

		// filtre sur la date
		if( $(":input[name=filter-date]").is(":checked") ) {		
			date  = $navigation.find(".date .values").data("month");
			date += "/";
			date += $navigation.find(".date .values").data("year");
		}

		// filtre sur l'heure
		// if( $(":input[name=filter-time]").is(":checked") ) {		
		// 		time  = $navigation.find(".time .clock").data("time");
		// }

		// enregistre les filtres
		$.cookie('user_list',  list,  { expires: 365, path:"/" });
		$.cookie('user_tags',  tags,  { expires: 365, path:"/" });
		$.cookie('user_date',  date,  { expires: 365, path:"/" });
		// $.cookie('user_time',  time,  { expires: 365, path:"/" });		
		/*/
		$.cookie('user_moods', moods, { expires: 365, path:"/" });
		
		// recharge le container
		window.site.loadPage("/");

	},


	/**
	 * @name allOptions
	 * @memberOf Site.fluxFilters
	 * @function
	 * @public
	 */
	allOptions: function() {

		var $this = $(this);
		// si le checkbox courant est checké
		if( $this.is(":checked") ) {
			// Coche tous les autres
			$this.parents(".filter").find("[type=checkbox]").prop("checked", true);								
			// Desactive le checkbox
			$this.prop("disabled", true);
		}

	}

};



/**
 * @name getNextPage
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.getNextPage = function($flux) {
	return $flux.find(".pagination a.next").attr("href") || undefined;
};


/**
 * @name scrollFlux
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.scrollFlux = function(event, scrollPositionX) {

	var $this = $(this);
		
	// reste à l'écoute du scroll jusqu'à atteindre le dernier block "page"
	if( scrollPositionX + $this.innerWidth() >= $this.find(".page:last").position().left ) {

		// supprime le bind sur le scroll
		$this.unbind("jsp-scroll-x");

		// récupère le lien vers la page suivante			
		var target = window.site.getNextPage( $this );

		// si on un lien, on charge la page
		if(target) {

			// ajoute l'id du flux courrant comme select
			if( $this.attr("id") ) {
				target += " #" + $this.attr("id");
			}
			
			// charge la page dans une div vide
			$("<div/>").load(target, function(data, status){ 
			    
			    // la requête est un succés
			    if(status == "success") {

			    	var $this = $(this),
			    		$flux = $("#" + $(this).children().attr("id") );

					// Sur les tweets de chargement
					$flux.find(".loading").remove();
					
			    	// on ajoute les posts
			    	$this.find(".wrapper .page").appendTo( $flux.find(".wrapper") );
			    	// change la pagination
			    	$flux.find(".pagination").replaceWith( $this.find(".pagination")  );

			    	// reinitialize le(s) flux
						window.site.initFlux();						
			    }

			});
		}
	}
}

/**
 * @name loadPage
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.loadPage = function(link) {
	
	// Si history.pushState est disponible et si on demande la page d'acceuil sans y être.
	if( !Modernizr.history || link.indexOf("wp-admin") > -1){
		window.location = link;
		// et on s'arrête
		return;
	}

	// Mode loading.
	$("#main").addClass("loading");

	// Charge la page dans une div vide.
	$("<div/>").load(link + " .content.current", function(data, status, xhr){ 
	   		   	
 
    	var $this       = $(this),
    		$main       = $("#main"),
    		$oldCard    = $main.find(".content.current"),
    		$document   = $(xhr.responseText),
    		$wpadminbar = $document.filter("#wpadminbar"),
    		root        = window.location.protocol+"//"+window.location.hostname+"/",
    		title       = $document.filter("title").text();    		
    		    	    	
		// Enlève le mode loading et supprime les blocks inutiles
		$main.removeClass("loading").find(".content.remove").remove();
				
	    // la requête est un succés
	    if(status == "success") {
    		
    		// si les animations/transitions sont dispo
    		if(Modernizr.csstransitions && Modernizr.cssanimations) {

	    		// on demande la page d'acceuil
	    		// slide vers la gauche
	    		if(link == "/" || link == root || link+"/" == root ) {    			

		    		// met l'ancien flux courrant de coté
		    		$oldCard.addClass("remove").removeClass("current");
		    		$main.addClass("go-right").removeClass("go-left");
		    		// ajoute le flux de la page d'acceuil
	    			$oldCard.before( $this.find(".content.current") );    			

	    		// n'importe qu'elle autre page
	    		// slide vers la droite
		    	} else {	    		
		    		// met l'ancien flux courrant de coté
		    		$oldCard.addClass("remove").removeClass("current");
		    		$main.addClass("go-left").removeClass("go-right");
		    		// ajoute le flux de la page d'acceuil
	    			$oldCard.after( $this.find(".content.current") );	
	    					
		    	}	

		    // si pas d'animation/transition
			} else {
				// on insert après l'ancien slide et on le supprime
				$oldCard.before( $this.find(".content.current") ).remove();
			}
			

    		// Si on a bien changé de page
    		if(link != window.location.href && link+"/" != window.location.href && Modernizr.history){
    			// Si on est pas en train de mettre à jour la page
				if(  !( link == "/" && window.location.href == root) ) {					
		    		// Modifie l'url
		    		window.history.pushState({}, title, link);				    		
					// Met a jour Google Analytics
					window.site.updateGa();					
					// Pour autoriser l'evenement popstate (hack chrome).
					// @src http://stackoverflow.com/questions/6421769/popstate-on-pages-load-in-chrome
					window.history.ready = true;
				}
			}    

			// Si il y a une wpadminbar, on la change
			if($wpadminbar.length == 1) $("#wpadminbar").replaceWith( $wpadminbar );

			// Change le titre de la page
			if(title) $("title").html(title);
			
	    	// Reinitialize le(s) flux
			window.site.initFlux();

		    // Scan les liens de partages
		    window.site.scanShare();
	    }

	});

}


/**
 * @name wrapperWidth
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.wrappersWidth = function() {	
	// pour chaque wrapper
	$('.flux .wrapper').each(function() {
		
		// Supprime le tweet de loading si il n'y a pas de page suivante
		if( $(this).parents(".flux").find(".pagination .next").length == 0 ) $(".loading", this).remove();	

		var $this 		  = $(this),
			pages_count   = $this.find(".page").length,
			page_width    = $this.find(".page").outerWidth() + 2,
			page_height   = $this.find(".page").outerHeight(),
			wrapper_width = pages_count * page_width;
		
				
		$this.css("height", page_height || 0);		
		
		if(pages_count > 3) {
			// attribut la bonne taille au wrapper
			$this.css("width", wrapper_width || 0);
		} else {
			$this.css("width", $(this).parents(".flux").innerWidth()  || 0);
		}	

		// donne a la dernière page la classe last
		$this.find(".page").removeClass("last").filter(":last").addClass("last");	

	});
};


/**
 * @name adaptTweetFontSize
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.adaptTweetFontSize = function() {	

	$(".flux .tweet.no-sized").each(function(i, tweet) {		
		
		var $tweet = $(tweet);
		// ajoute une class en fonction du nombre de caractères (de 25 en 25, 4 jeux de tailles différents)
		$tweet.removeClass(".no-sized").addClass("size-" + ~~($tweet.find(".content a").text().length/30) );		

	});
	
};

/**
 * @name toggleFilters
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.toggleFilters = function(event) {	

	// bloque l'évènement par défaut
	event.preventDefault();

	// supprime la classe close si elle est présente (vice-versa)
	$(".filters").toggleClass("close");
};

/**
 * @name toggleSwitcher
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.toggleSwitcher = function(event) {	

	// bloque l'évènement par défaut
	event.preventDefault();

	var $this = $(this);	
	// supprime la classe off si elle est présente (vice-versa)
	$this.toggleClass("off");

	// met à jour le flux
	$.cookie('user_random',    1*!$(".random.switcher").hasClass("off"), { expires: 365, path:"/" });	
	$.cookie('user_bookmarks', 1*!$(".stared.switcher").hasClass("off"), { expires: 365, path:"/" });	
	window.site.loadPage("/");
};



/**
 * @name drawDate
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.drawDate = function() {	

	var $date  	     = $(".navigation .date"),
 		$values      = $date.find(".values"),
		$month 	     = $values.find(".val:eq(0)"),
		$monthPicker = $(".picker.month"),
		month 	     = $values.data("month") < 10 ? "0"+$values.data("month") : $values.data("month"),		
		monthWord    = "",
		$year        = $values.find(".val:eq(1)"),
		$yearPicker  = $(".picker.year"),
		year 	     = $values.data("year"),
		monthYear    = "";

	// Trouve le "mot" correspondant au mois
	$monthPicker.find("li").each(function() {
		var $this = $(this);
		if( $this.data("month") == month)
			monthWord = $this.text();
	});

	// Trouve le "mot" correspondant à l'année
	$yearPicker.find("li").each(function() {
		var $this = $(this);
		console.log(year, $this.data("year"));
		if( $this.data("year") == year)
			yearWord = $this.text();
	});


	// concatène 0 aux mois inférieurs à 10
	$month.find(".display").html(month);
	$monthPicker.find(".current").html(month).append( $("<div/>").addClass("word").html(monthWord) );

	$year.find(".display").html(year);
	$yearPicker.find(".current").html(year).append( $("<div/>").addClass("word").html(yearWord) );

};

/**
 * @name drawClock
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.drawClock = function() {	

	var $clock       = $(".navigation .clock"),
		$hour        = $clock.find(".hour"),
		$minute      = $clock.find(".minute"),
		$slider      = $(".navigation .time .slider"),
		$cursor      = $slider.find(".cursor"),		
		$value       = $(".navigation .time .values .display"),
		$picker      = $(".navigation .time .picker"),
		originalTime = $clock.data("time"),
		// l'heure est stockée en meta data sur l'horloge
		time 	      = originalTime.split("h"),
		// calcul le nombre de minutes que représente l'heure
		nbMinutes     = time[0]*60 + 1*time[1],
		// calcul le pourcentage d'avancement du slider
		progCursor    = nbMinutes/(24*60)*100,
		// pour remettre les pendules à l'heure
		// il faut avancer les heures de 60 degrés
		hourDeg       = 60,
		// et reculer les minutes de 45 degrés
		minDeg        = -45,
		// mot correspondant à l'interval de l'heure (à déterminer)
		word 	      = "";
		
	// ajuste le curseur du slider
	$cursor.css("left", progCursor+"%" );

	// met à jour la valeur
	$value.html(time[0] + "h" + time[1]);	

	// calcul le degree des minutes
	time[1] /= 60;
	minDeg  += time[1] * 360;	

	// calcul le degree des heures
	time[0] -=  time[0] >= 12 ? 12 : 0;
	time[0] /= 12;
	hourDeg += time[0] * 360 + (360/12*time[1]);	
			
	// ajuste les éguilles
	$hour.css("rotate",   hourDeg+"deg");
	$minute.css("rotate", minDeg+"deg");

	// Détermine le mot correspondant à l'interval de l'heure 	
	$picker.find("li").each(function() {
		var $this = $(this);				
		if( $this.data("time") == originalTime)
			word = $this.data("word");
	});
	
	// Mise à jour du menu
	$(".time .current").html( $clock.data("time") ).append( $("<div/>").addClass("word").html( word ) );
};

/**
 * @name clockMswheel
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.clockMswheel = function(event, delta) {
	
	// stop l'action de srcoll 
	event.preventDefault();

	var $clock   = $(".navigation .clock"),
		// l'heure est stockée en meta data sur l'horloge
		time 	 = $clock.data("time").split("h"),
		// nombre total des minutes
		ttMinute = time[0]*60+time[1]*1;
	
	
	// augmente les minutes en fonction du delta est arrondie
	ttMinute += ~~(20 * delta);

	// limite les minutes
	ttMinute = ttMinute < 0 ? 0 : (ttMinute > 24*60 ? 24*60-1 : ttMinute); 

	// déduit les heures
	time[0] = Math.floor(ttMinute/60);
	// et les minutes
	time[1] = ttMinute - (time[0]*60);

	// ajoute des 0 devant les chiffres inférieur à 10
	time[0] = time[0] < 10 ? "0"+time[0] : time[0];
	time[1] = time[1] < 10 ? "0"+time[1] : time[1];

	// change l'heure
	$clock.data("time", time.join("h"));

	// redessine l'horloge
	site.drawClock();
};


/**
 * @name fieldWarning
 * @memberOf Site
 * @function
 * @public
 * @param {jQuery} field
 * @return {jQuery}
 */
Site.prototype.fieldWarning = function($field) {
	return $field.parents("label").find(".field-warning");
};


/**
 * @name toggleBookmark
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.toggleBookmark = function() {
	
	var $this = $(this),
		post  = $this.data("post-id");


	if( $this.hasClass("on") ) {
		$.get("?action=remove-bookmark&post-id="+post);
	} else {
		$.get("?action=add-bookmark&post-id="+post);
	}

	// change la classe de l'élément
	$this.toggleClass("on");

}


/**
 * @name checkEmail
 * @memberOf Site
 * @function
 * @public
 * @param {String} email
 * @return {Boolean}
 */
Site.prototype.checkEmail = function(email) {

	// une regex glamour pour vérifier le format de l'email
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\ ".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA -Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

  return re.test(email);

};


/**
 * @name signup_checkField
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.signup_checkField = function() {
    // formulaire
    $signUp   = $("#sign-up"),
    $password = $signUp.find("[name=password]"),
    $email    = $signUp.find("[name=email]"),
    ok 		  = true;

    // si le password est trop petit
    if( $password.val().length < 6 ) {
    	// on affiche l'avertissement
        this.fieldWarning($password).removeClass("hidden").html( this.fieldWarning($password).data("format") );
        // et on retournera false
        ok = false;
    } else {
    	// sinon on cache l'avertissement
        this.fieldWarning($password).addClass("hidden");
    }

    // si l'email n'est pas au bon form
    if( !this.checkEmail( $email.val() ) ) {    	
    	// on affiche l'avertissement
        this.fieldWarning($email).removeClass("hidden").html( this.fieldWarning($email).data("format") );;
        // et on retournera false
        ok = false;
    } else {
    	// sinon on cache l'avertissement
        this.fieldWarning($email).addClass("hidden");
    }
    
    return ok;
};


/**
 * @name user_update_checkField
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.user_update_checkField = function() {
    // formulaire
    $signUp   = $("#user-update-data"),
    $password = $signUp.find("[name=password-new]"),
    $email    = $signUp.find("[name=email]"),
    ok 		  = true;

    // si le password est trop petit
    if( $password.val().length > 0 && $password.val().length < 6 ) {    	
    	// on affiche l'avertissement
        this.fieldWarning($password).removeClass("hidden").html( this.fieldWarning($password).data("format") );
        // et on retournera false
        ok = false;
    } else {
    	// sinon on cache l'avertissement
        this.fieldWarning($password).addClass("hidden");
    }

    // si l'email n'est pas au bon form
    if( !this.checkEmail( $email.val() ) ) {    	
    	// on affiche l'avertissement
        this.fieldWarning($email).removeClass("hidden").html( this.fieldWarning($email).data("format") );;
        // et on retournera false
        ok = false;
    } else {
    	// sinon on cache l'avertissement
        this.fieldWarning($email).addClass("hidden");
    }
    
    return ok;
};

/**
 * @name update_ga
 * @memberOf Site
 * @function
 * @public
 */
Site.prototype.updateGa = function() {
	window._gaq.push(['_trackPageview', window.location.href.split(window.location.hostname)[1] ]);
};


Site.prototype.scanShare = function() {	
	// Ajoute des évènements pour que les liens ouvrent des popups
	$("a.fb").share("facebook");
	$("a.tw").share("twitter");
	$("a.pn").share("pinterest");
	$("a.gp").share("google");
};



(function(window, undefined) {

    window._gaq = [['_setAccount','UA-27550683-1'],['_setDomainName', 'aucun-lien.com'],['_setAllowLinker', true],['_trackPageview'],['_trackPageLoadTime']];

    Modernizr.load({
        load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
    });
	

    window.site = new Site(window.jQuery);   

})(window); 