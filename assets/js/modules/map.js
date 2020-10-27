import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

export default class Map {

    static init () {
        let map = document.querySelector('#map')
        if (map === null) {
            return
        }

        // créer l'icône
        let icon = L.icon({
            iconUrl: '/images/marker_map.png',
        });

        let center = [map.dataset.lat, map.dataset.lng]

        // L.map('map') = create map with id map
        map = L.map('map').setView(center,13)

        let token = 'pk.eyJ1IjoicmFkb2lib2dkYW4iLCJhIjoiY2tnbnZ0OGZ4MHlzdDJ4bzFiejMxNzlzbyJ9.CCwU9kzFTg5YEcklJmA0tg'
        L.tileLayer(`https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=${token}`,{
            maxZoom: 18,
            minZoom: 12,
            attribution: '© <a href=\'https://www.mapbox.com/about/maps/\'>Mapbox</a> © <a href=\'http://www.openstreetmap.org/copyright\'>OpenStreetMap</a>'
        }).addTo(map)

        L.marker(center, {icon : icon}).addTo(map)
    }
}