function init() {

    var Lng = document.getElementById('Lng').value;
    var Lat = document.getElementById('Lat').value;
    var aleas = document.getElementById('aleas').value;

    console.log(aleas);

    var WMSLink = "http://ws.carmen.developpement-durable.gouv.fr/WMS/8/risques_naturels_inondation";

    var overlayMaps = {};

    const firstView = {
        lat: Lat,
        lgn: Lng
    }

    const zoomLevel = 14;

    const map = L.map('map').setView([firstView.lat, firstView.lgn], zoomLevel);

    const mainLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'OpenStreetMap',
    }).addTo(map);

    if (aleas.includes("pprn")) {
        const pprnLayer = L.tileLayer.wms(WMSLink, {
            layers: 'L_PPRN_S_R28',
            transparent: true,
            opacity: 0.80,
            format :"image/png",
            crs: L.CRS.EPSG4326,
            attribution: "PPRN",
        }).addTo(map);
        overlayMaps["PPRN"] = pprnLayer;
    } 
    if(aleas.includes("debordement")) {
        const ziLayer = L.tileLayer.wms(WMSLink, {
            layers: 'L_ZI_S_R25',
            transparent: true,
            opacity: 0.80,
            format :"image/png",
            crs: L.CRS.EPSG4326,
            attribution: "Zone inondable (14, 50, 61)",
        }).addTo(map);
        overlayMaps["Debordement de cours d'eau"] = ziLayer;
    }
    if(aleas.includes("cavité")) {
        const ziLayer = L.tileLayer.wms("https://ws.carmen.developpement-durable.gouv.fr/WMS/8/risques_naturels_mvt", {
            layers: 'L_CAVITES_P_R28',
            transparent: true,
            opacity: 0.80,
            format :"image/png",
            crs: L.CRS.EPSG4326,
            attribution: "Cavités (14, 50, 61)",
        }).addTo(map);
        overlayMaps["Presence de cavités souterraines"] = ziLayer;
    }

    var baseMaps = {
        "Maps": mainLayer
    };

    var layerControl = L.control.layers(baseMaps, overlayMaps).addTo(map);

    L.marker([Lat, Lng]).addTo(map);

    L.control.scale().addTo(map);
    var switcher = L.geoportalControl.LayerSwitcher();
    map.addControl(switcher);
}