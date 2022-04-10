/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const $ = require('jquery');

global.$ = global.jQuery = $;

require('bootstrap');

require('select2');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

$('.select2').select2({
    tags: true,
    tokenSeparators: [',', '  ']
});

import 'select2/dist/css/select2.min.css';

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';
