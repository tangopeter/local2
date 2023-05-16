// console.log("complete order pagess");

// jQuery(document).ready(function ($) {
//   if (typeof acf !== 'undefined') {
//     console.log('ACF is defined', acf);
//   }
// });



export function completeMyOrder() {
  // var field = acf.getField('field_640d5fdec5c14');
  // console.log(field);
// console.log("js " + document.title);
// jQuery(document).ready(function ($) {
  
//   if (typeof acf !== 'undefined') {
//     console.log('ACF is defined  vv', acf);
//   }
//   else {
//     console.log('ACF not defined', acf);
//   }
// });

  let dm = (Number(4.50)).toFixed(2);
  let rp = (Number(0.00)).toFixed(2);
  let sd = (Number(0.00)).toFixed(2);
  let total = (Number(0.00)).toFixed(2);



  const printPrice = document.getElementById('totalPrintPrice');
  const printPriceCost = document.getElementById('acf-field_640d5fdec5c14');


  const deliveryMethod = document.getElementById("acf-field_640d61d5084e4");
  const rural = document.getElementById("acf-field_640d61d5084ec");
  const saturday = document.getElementById("acf-field_640d61d508503");
  const toPostal = document.getElementById("acf-field_640d61d50850d");

  // const finalizeOrder = document.getElementById('completeOrder');
  // finalizeOrder.addEventListener('click', finalizeTheOrder);

  deliveryMethod.addEventListener('change', getDeliveryMethod);
  rural.addEventListener('change', checkRuralDelivery);
  saturday.addEventListener('change', checkSatDelivery);
  toPostal.addEventListener('click', toPostalClick)
  // completeTheOrder.addEventListener('click', completeThisOrder)


  const deliveryMethodPrice = document.createElement("p");
  const ruralPrice = document.createElement("p");
  const saturdayPrice = document.createElement("p");

  // Add a Delivery method Cost field
  // deliveryMethodPrice.textContent = dm;
  deliveryMethodPrice.classList.add('delPriceMethod');
  deliveryMethod.insertAdjacentElement('afterEnd', deliveryMethodPrice);
  // Add a Rural Delivery cost field
  ruralPrice.textContent = rp;
  ruralPrice.classList.add('ruralPrice');
  rural.insertAdjacentElement('afterEnd', ruralPrice);
  // Add a Saturday Delivery cost field
  saturdayPrice.textContent = sd;
  saturdayPrice.classList.add('satPrice');
  saturday.insertAdjacentElement('afterEnd', saturdayPrice);

  function finalizeTheOrder() {
    console.log("finalizeOrder");
  }
  // get the print cost total and update 
  var pp = printPrice.textContent;
  pp = (Number(pp).toFixed(2));
  console.log("pp = ", pp);
  printPrice.textContent = pp;
  // console.log(printPriceCost);

  // var pc = printPriceCost.value;
  // console.log("pc = ", pc);

  // get the delivery method cost and update 
  function getDeliveryMethod(event) {
    var dm = event.target.value;
    deliveryMethodPrice.textContent = dm;
    // updateOrderValue();
  }
  // Get the Rural delivery and cost
  function checkRuralDelivery(event) {
    var rp = 0;
    console.log(event)

    
    let checkBox = acf.getField('field_640d61d5084ec');
    // if (checkBox.val() == 1) {
    //   rp = 4.50;
    // } else {
    //   rp = 0.00;
    // }
    // console.log("rp = ", rp);
    // let rp1 = (Number(rp)).toFixed(2)
    // updateNumberWFUField2(ruralPrice, rp1);
    // updateOrderValue();
  }

  // Get the Saturday delivery and cost
  function checkSatDelivery(event) {
    var sd = 0;
    let checkBox = acf.getField('field_640d61d508503');
    if (checkBox.val() == 1) {
      sd = 5.00;
    } else {
      sd = 0.00;
    }
    // console.log("sd = ", sd);
    let sd1 = (Number(sd)).toFixed(2)
    updateNumberWFUField2(saturdayPrice, sd1);
    // updateOrderValue();
  }
  // Send to postal address
  function toPostalClick(event) {
    const mailToAddress = document.getElementById("mailAddress");
    mailToAddress.classList.toggle("showField");
  }
}
function updateOrderValue() {
  // Print Cost:
  var printCost = acf.getField('field_640d5fdec5c14');
  console.log('printCost: ', printCost.val());

  const parentDOM = document.getElementById("deliveryCosts");
  const deliveryMethodPrice = parentDOM.getElementsByClassName("delPriceMethod")[0].innerText;
  console.log('deliveryMethodPrice', deliveryMethodPrice);

  const ruralPrice = parentDOM.getElementsByClassName("ruralPrice")[0].innerText;
  console.log('Rural Cost: ', ruralPrice);

  const satPrice = parentDOM.getElementsByClassName("satPrice")[0].innerText;
  console.log('Sat Cost: ', satPrice);

  var deliveryTotal =
    Number(deliveryMethodPrice) +
    Number(ruralPrice) +
    Number(satPrice);

  const totalDeliveryCost = acf.getField('field_640d604cc5c15');
  console.log('total deliveryCost: ', deliveryTotal.toFixed(2));
  totalDeliveryCost.val(deliveryTotal.toFixed(2));

  var total = Number(printCost.val()) +
    Number(deliveryMethodPrice) +
    Number(ruralPrice) +
    Number(satPrice);
  console.log('__________________________________');
  console.log('total: ', total.toFixed(2));

  var subTotal = acf.getField('field_641d71ff02321');
  subTotal.val(total.toFixed(2));

  var gstCost1 = (Number(total.toFixed(2)) * Number(0.15));
  console.log('+GST: ', gstCost1.toFixed(2));

  var gst = acf.getField('field_640d6074c5c16');
  gst.val(gstCost1.toFixed(2));

  var finalCost = Number(total.toFixed(2)) + Number(gstCost1);
  var totalCost = acf.getField('field_640d609fc5c17');
  totalCost.val(finalCost.toFixed(2));

  console.log('>---------------------------------');
  console.log('finalCost: ', finalCost.toFixed(2));
  console.log('>---------------------------------');
}

function updateNumberWFUField2(fieldName, newValue) {
  // console.log("updateNumberWFUField: ", fieldName.className, ", ", newValue);
  let nn = Number(newValue).toFixed(2);
  fieldName.textContent = nn;
  // console.log("updateNumberWFUField: ", fieldName,  newValue, fieldName.textContent);
}

function completeThisOrder() {
  console.log("completeThisOrder");


  console.log("1/ update all the uploaded files ");

  console.log("2/ collect all the user & delivery data");

  console.log("3/ Write to new entry in completed orders table");




}