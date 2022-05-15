function confirmationEtActualisation(message) {
    console.log("lol");
    if(confirm("Êtes-vous sûr de vouloir le " + message + "? ")){
        window.location.reload();
        document.getElementById("supVal").setAttribute("value", 1);
    } else {
        document.getElementById("supVal").setAttribute("value", 0);
    }
}