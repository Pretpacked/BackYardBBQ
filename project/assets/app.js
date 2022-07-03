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

    // function get all items in cart
    $.get("http://localhost:8000/cart/get", function( i ) {
        if(i['data'] !== null){
            document.getElementById('cart-container').innerHTML = '<a id="cart" href="http://localhost:8000/cart/clear">'+
            '<i class="fa-solid fa-cart-shopping"></i>'+
            '<div id="cart_number"></div></a>'
            document.getElementById('cart_number').innerHTML = i['data'].length;

        }
    });

    // Function for loading the orders page table
    $.get( "http://localhost:8000/api/orders", function( data ) {
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
      $.get( "http://localhost:8000/api/orders", function( data ) {
        data = JSON.parse(data['data']);
        const table = document.getElementById('renting_table_custom')
        console.log(data)
        document.querySelector('#loader').style.display = 'none';
        for (let i = 0; i < data.length; i++) {
            const element = data[i];
            table.innerHTML += '<a class="rent-barbecue-container" href="http://localhost:8000/bqq/overview/'+ element['id'] +'">'+
            '<div >'+
                '<img src="/uploads/'+element['image']+'"></div>'+
                '<div class="barbecue-title">'+element['name']+'</div></a>';
        }
    });
    $('.cardRemove').click(function(){
        console.log($(this).attr('id'))
        $.post('http://localhost:8000/cart/remove/'+$(this).attr('id')+'', function() {
            location.reload();
        })
    })

});