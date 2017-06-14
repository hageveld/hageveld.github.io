var kandidaten = [{
	id: 1,
	naam: 'Bibi Wijs',
	klas: '3D',
},
{
	id: 2,
	naam: 'Evelien van der Geest',
	klas: '3D',
},
{
	id: 3,
	naam: 'Hannah Rakers',
	klas: '3C',
},
{
	id: 4,
	naam: 'Laura Mastwijk',
	klas: '3B',
},
{
	id: 5,
	naam: 'Romy van Leeuwen',
	klas: '4G',	
}];

function shuffle(array) {
    var currentIndex = array.length,
        temporaryValue, randomIndex;
    while (0 !== currentIndex) {
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;
        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
    }
    return array;
}

function connectToMagister() {
    $.get("https://lu1t.nl/magisterauth.php?username=" + encodeURIComponent($("#username").val()) + "&password=" + encodeURIComponent($("#password").val()), function(data) {
            userinfo = JSON.parse(data);
            if (userinfo.error) {
                $("#magister").fadeOut("", function() {
                    $("#error").fadeIn();
                });
            } else if (userinfo.success && !userinfo.error && !userinfo.ingevuld) {
                $("#magister").fadeOut("", function() {
					var keuzes = shuffle(kandidaten);
					$.each(keuzes, function(i,data) {
						$("#stem").append("<p><input class='with-gap' name='stem' type='radio' id='" + data.id + "'  /><label for='" + data.id + "'>" + data.naam + "</label></p>");
					});
                    $("#stemmen").fadeIn();
                });
			} else if(userinfo.gestemd) {
				$(".magisterspinner").fadeOut("", function() {
                    $(".section.magister").fadeIn();
                    Materialize.toast('Je hebt al gestemd!', 6000)
                });
            } else {
                setTimeout(function() {
                    $(".magisterspinner").fadeOut("", function() {
                        $(".section.magister").fadeIn();
                        Materialize.toast('Verkeerde gebruikersnaam of wachtwoord!', 4000)
                    });
                }, 1000);
            }
        })
        .fail(function() {
            setTimeout(function() {
                $(".magisterspinner").fadeOut("", function() {
                    $(".section.magister").fadeIn();
                    Materialize.toast('Geen internetverbinding', 4000)
                });
            }, 1000);
        })
}

function finishUp() {
		$.get("https://lu1t.nl/stem.php?stem=" + encodeURIComponent($('input[name=stem]:checked', '#stem').attr('id')), function(data) {
            $("#stemmen").fadeOut("", function() {
                $("#einde").fadeIn();
            });
        })
        .fail(function() {
            setTimeout(function() {
                Materialize.toast('Geen internetverbinding', 4000)
            }, 1000);
        })
}
$(document).ready(function() {
    $(window).keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    $("#beginnen").click(function() {
		$("html, body").animate({ scrollTop: 0 }, "slow");
        $("#start").fadeOut("", function() {
            $("#magister").fadeIn();
        });
    });
    $("#login").click(function() {
		$("html, body").animate({ scrollTop: 0 }, "slow");
        $(".section.magister").fadeOut("", function() {
            $(".magisterspinner").fadeIn();
        });
        connectToMagister();
    });
    $("#klaar").click(function() {
        finishUp();
    });
});