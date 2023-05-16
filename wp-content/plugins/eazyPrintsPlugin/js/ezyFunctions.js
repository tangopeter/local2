// console.log("ezyFunctions");

const orderNumber = document.getElementById('userdata_2_field_1');
const previousOrder = document.getElementById("previous_order");
const completeOrder = document.getElementById("complete_order");
const nextOrder = document.getElementById("next_order");

// previousOrder.addEventListener('click', viewPreviousOrder);
// completeOrder.addEventListener('click', completeTheOrder);
// nextOrder.addEventListener('click', viewNextOrder);

function viewNextOrder() {
  console.log(orderNumber.value);
  console.log("viewNextOrder");
  var order = orderNumber.value;
  order++;
  console.log(order);
  orderNumber.value = order;
  increaseOrderNumberOption(order);

}


function viewPreviousOrder() {
  console.log("viewPreviousOrder");
  var order = orderNumber.value;
  order--;
  console.log(order);
  orderNumber.value = order;
}


function completeTheOrder() {
  console.log("complete The Order");
  // console.log("orderNumber: ", WPVars.orderNumber);


}

function increaseOrderNumberOption(order) {
  console.log("heelloo", order)
  jQuery(document).ready(function($) {
    // var order = 6987;
    // console.log("testy: ", order);
    // // $.ajax({
    //    url: '/wp-admin/admin-ajax.php',
    //    data: {
    //      'action': 'copyOrderNumber',
    //      'orderNumber': order
    //   },
    // success: function(data){
    //   console.log("heelloo")
    //   }
    });
  // });

}