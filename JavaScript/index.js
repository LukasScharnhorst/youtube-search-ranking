// Overlay-Fenster öffnen
function on_overlay() {
    document.getElementById("overlay").style.display = "block";
}

// Overlay-Fenster schließen
function off_overlay() {
    document.getElementById("overlay").style.display = "none";
    //location.reload();
}

// Hinweis-Fenster öffnen
function on_hinweis(keyword) {
    document.getElementById("hinweis").style.display = "block";
    const popup = document.querySelector("#popup");
    popup.innerHTML +=      "<p class=\"u-margin-bottom-0_75rem u-font-size-l u-color-dark-grey\">folgendes Keyword wirklich löschen?</p>" + "<p class=\"u-font-size-xl u-margin-bottom-2rem u-font-weight-bold u-color-dark-grey\">" + keyword.value + "</p>";
    popup.innerHTML +=      "<input type='hidden' name='zu_loeschendes_keyword' value='" + keyword.value + "'>";
}

// Hinweis-Fenster schließen
function off_hinweis() {
    document.getElementById("hinweis").style.display = "none";

    /*
        Problem vorher:
            Wenn man ein Keyword löschen möchte und auf das entsprechende X-Zeichen klickt, jedoch
            das Löschen im Popup-Fenster mit dem Klicken auf "Nein" wieder abbricht, öffnet sich
            beim nächsten Klicken auf ein X-Zeiche nicht nur das popup-Fenster zum Löschen für das aktuelle
            Keyword, sondern auch die popup-Fenster für alle Keywords, auf deren X-Zeichen man schon
            irgendwann vorher geklickt hatte

        mit dem Reload der Seite behebt sich das Problem
     */
    location.reload(true);
}

function channel_speichern_hinweis() {
    window.alert("Das Speichern der Channel_ID war erfolgreich");
}

function channel_vorhanden_hinweis() {
    window.alert("Die Channel-ID ist schon vorhanden");
}

document.addEventListener("DOMContentLoaded", function() {

    let menuButton = document.querySelector('[data-menu="button"]');
    let menu = document.querySelector('[data-menu="menu"]');
    let body = document.querySelector('[data-menu="overflow-hidden"]');

    menuButton.addEventListener('click', () => {
        menuButton.classList.toggle('is-active');
        menu.classList.toggle('show');
        body.classList.toggle('overflow-hidden');
    });

});

/*document.addEventListener("DOMContentLoaded", function() {

    let page = document.querySelector('[data-menu="current-page"]');

    page.addEventListener('click', () => {
        page.classList.toggle('color-red');
    });

});*/