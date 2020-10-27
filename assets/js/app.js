/*
 * Welcome to your app's main JavaScript file!
 * * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 * Any CSS you import will output into a single css file (app.css in this case)
 */
import '../css/app.css';

import Places from 'places.js'
import Map from './modules/map'
import 'slick-carousel'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

Map.init()

// PropertyType dans le admin/property/_form.html.twig
// Après le choix de l'adresse par le user, les champs ci-dessous sont automatiquement modifiés (city etc.)
let inputAddress = document.querySelector('#property_address');
if(inputAddress !== null) {
    let place = Places({
        container: inputAddress // l'élément qui sera remplace par la système d'autocompletion
    })
    place.on('change', e => {
        document.querySelector('#property_city').value = e.suggestion.city
        document.querySelector('#property_postal_code').value = e.suggestion.postcode
        document.querySelector('#property_lat').value = e.suggestion.latlng.lat
        document.querySelector('#property_lng').value = e.suggestion.latlng.lng
    })
}

// PropertySearchType dans le property/index.html.twig
// Change les valeurs des champs hidden qui seront utilisées par le PropertyRepository pour retrouver les biens dans une zone définie
let searchAddress = document.querySelector('#search_address');
if(searchAddress !== null) {
    let place = Places({
        container: searchAddress // l'élément qui sera remplace par la système d'autocompletion
    })
    place.on('change', e => {
        document.querySelector('#lat').value = e.suggestion.latlng.lat
        document.querySelector('#lng').value = e.suggestion.latlng.lng
    })
}

let $ = require('jquery');
import '../css/app.css';
import 'select2';

$('[data-slider]').slick({
    dots: true,
    arrows: true
});

$('select').select2();
let $contactButton = $('#contactButton');
$contactButton.click(e => {
    e.preventDefault()
    $('#contactForm').slideDown();
    $contactButton.slideUp();
})

// Suppression des éléments _form.html.twig
document.querySelectorAll('[data-delete]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault()
        fetch(a.getAttribute('href'), {
            method : 'DELETE',
            headers : {
                'X-Requested-With' : 'XMLHttpRequest',
                'Content-Type' : 'application/json'
            },
            body : JSON.stringify({'_token': a.dataset.token}) // method converts a JavaScript object or value to a JSON string

        })
            // Body.json() => transforms JSON into a JavaScript object
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    a.parentNode.parentNode.removeChild(a.parentNode)
                } else {
                    alert(data.error)
                }
            })
            .catch(e => alert(e)) // erreur au niveau du parsing du json ou si le serveur ne répond pas
    })
})

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/app.js');