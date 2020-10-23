/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
let $ = require('jquery');
import './styles/app.css';
import 'select2';

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