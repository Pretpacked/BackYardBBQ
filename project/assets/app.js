/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

$(document).ready(function() {
    // Function for loading the orders page table
    $.get( "api/orders", function( data ) {
        data = JSON.parse(data['data']);
        // Loop through the data object recieved to generate the table
        for (let i = 0; i < data.length; i++) {
            const element = data[i];
            var customer;
            document.querySelector('#loader').style.display = 'none';

            //Add row to the table with the data from this barbecue
            $('#ordersTable tr:last').after('<tr><th>'+(i+1)+'</th>'+
            '<th>'+element['name']+'</th><th>'+element['description']+'</th>'+
            '<th>€'+element['barbecuePrice']+'</th><th>'+element['image']+'</th><th>'+
            '<a href="api/remove/'+element['id']+'"><button type="button" class="btn btn-warning">remove</button></a></th></tr>');
            }
    });

      // Function showing the rent page barbecue for customers to order from.
      $.get( "api/orders", function( data ) {
        data = JSON.parse(data['data']);
        const table = document.getElementById('renting_table_custom')
        console.log(data)
        document.querySelector('#loader').style.display = 'none';
        for (let i = 0; i < data.length; i++) {
            const element = data[i];
            table.innerHTML += '<a class="rent-barbecue-container" href="bqq/overview/'+ element['id'] +'">'+
            '<div >'+
                '<img src="/uploads/'+element['image']+'"></div>'+
                '<div class="barbecue-title">'+element['name']+'</div></a>';
        }
    });
});