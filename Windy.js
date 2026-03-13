const options = {
    // Required: API key
    key: 'CBrZqQ2uC5nrDaGTwtUYna2m7e9502Jn', // REPLACE WITH YOUR KEY !!!

    // Put additional console output
    verbose: true,
    
    // Optional: Initial state of the map
    lat: 5.8197935,
    lon: 100.3848697,
    overlay: 'rain',
    favOverlays: ["rain","rainAccu"],
    zoom: 10,
    
};


// Initialize Windy API
windyInit(options, windyAPI => {
    // windyAPI is ready, and contain 'map', 'store',
    // 'picker' and other usefull stuff



    const { map } = windyAPI;
    // .map is instance of Leaflet map

    L.popup()
        .setLatLng([5.8197935, 100.3848697])
        .setContent('Hello World')
        .openOn(map);


});



