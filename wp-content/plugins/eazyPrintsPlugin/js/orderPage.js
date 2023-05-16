  //  console.log("2 order page");

  const thisJobNumber = document.getElementById("userdata_1_field_0");

  const selectFiles = document.getElementById("fileName_2");

  const sizefinish = document.getElementById("userdata_1_field_3");
  const printQty = document.getElementById("userdata_1_field_4");
  const reSize = document.getElementById("userdata_1_field_5");
  const eachPrice = document.getElementById("userdata_1_field_6");
  const printPrice = document.getElementById("userdata_1_field_7");

  const files = document.getElementById("userdata_1_field_8");
  const totalPrints = document.getElementById("userdata_1_field_9");
  const totalPrintsCost = document.getElementById("userdata_1_field_10");

  const size = document.getElementById("userdata_1_field_11");
  const finish = document.getElementById("userdata_1_field_12");

  const deliveryMethod = document.getElementById("userdata_1_field_13");
  const rural = document.getElementById("userdata_1_field_14");
  const saturday = document.getElementById("userdata_1_field_15");
  const toPostal = document.getElementById("userdata_1_field_16");

  const printCost = document.getElementById("userdata_1_field_19");
  const deliveryCost = document.getElementById("userdata_1_field_20");
  const gstCost = document.getElementById("userdata_1_field_21");
  const totalCost = document.getElementById("userdata_1_field_22");

  const deliveryMethodPrice = document.createElement("p");
  const ruralPrice = document.createElement("p");
  const saturdayPrice = document.createElement("p");

  let dm = (Number(4.50)).toFixed(2);
  let rp = (Number(0.00)).toFixed(2);
  let sd = (Number(0.00)).toFixed(2);
  let total = (Number(0.00)).toFixed(2);
  
  export function orderPage() {

    // console.log("2a order page");
      deliveryMethod.addEventListener('change', getDeliveryMethod);
      rural.addEventListener('change', checkRuralDelivery);
      saturday.addEventListener('change', checkSatDelivery);
      toPostal.addEventListener('click', toPostalClick)

      // Add a Delivery method Cost field
      deliveryMethodPrice.textContent = dm;
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
   
      // Get the image settings changes
      // selectFiles.addEventListener('change', getFiles);
      sizefinish.addEventListener('change', getOrderSize);
      reSize.addEventListener('change', getOrderSize);
      printQty.addEventListener('input', getOrderSize);
}



  // Get the delivery method and cost
  export function getDeliveryMethod(event) {
    switch (event.target.value) {
      case 'Post Env to 10x8 - 4.50':
        dm = 4.50;
        break;
      case 'Courier local (Wellington) - 7.00':
        dm = 7.00;
        break;
      case 'Courier North Island - 13.00':
        dm = 13.00;
        break;
      case 'Courier South Island - 28.00':
        dm = 28.00;
        break;
      case 'Courier Waiheke Island - 26.00':
        dm = 26.00;
        break;
      case 'no charge by arrangement - 0.00':
        dm = 0.00;
        break;
    }
    let dm1 = (Number(dm)).toFixed(2);
    updateNumberWFUField2(deliveryMethodPrice, dm1);
    UpdateOrderTotals();
  };
  // Get the Rural delivery and cost
  export function checkRuralDelivery() {
    let checkBox = document.getElementById('userdata_1_field_14');
    if (checkBox.checked == true) {
      rp = 4.50;
    } else {
      rp = 0.00;
    }
    let rp1 = (Number(rp)).toFixed(2)
    updateNumberWFUField2(ruralPrice, rp1);
    UpdateOrderTotals();
  }
  // Get the Saturday delivery and cost
  export function checkSatDelivery() {
    let checkBox = document.getElementById('userdata_1_field_15');
    if (checkBox.checked == true) {
      sd = 5.00;
    } else {
      sd = 0.00;
    }
    let sd1 = (Number(sd)).toFixed(2)
    updateNumberWFUField2(saturdayPrice, sd1);
    UpdateOrderTotals();
  }
  // Send to postal address
  function toPostalClick() {
    console.log('-->  toPostal');
  }
  // Array for the prices
  var printPrices = new Array();
  var i0 = ["dummy", 0.00, 0.50, 1.00, 1.50, "NA", "NA"];
  var i1 = ["5x7_Lustre", 1.20, 0.95, 0.80, "5x7", "Lustre"];
  var i2 = ["5x7.5_Lustre", 1.20, 0.95, 0.80, "5x7.5", "Lustre"];
  var i3 = ["5x11_Lustre", 3.00, 3.00, 3.00, "5x11", "Lustre"];
  var i4 = ["5x11_Laminated", 3.00, 3.00, 3.00, "5x11", "Laminated"];
  var i5 = ["6x4_Lustre", 0.25, 0.25, 0.25, "6x4", "Lustre"];
  var i6 = ["6x4.5_Lustre", 0.30, 0.30, 0.30, "6x4.5", "Lustre"];
  var i7 = ["6x8_Lustre", 2.80, 2.50, 2.00, "6x8", "Lustre"];
  var i8 = ["6x9_Lustre", 2.80, 2.50, 2.00, "6x9", "Lustre"];
  var i9 = ["8x6_Supreme", 3.00, 3.00, 3.00, "8x6", "Supreme"];
  var i10 = ["8x12_Supreme", 5.50, 5.50, 5.50, "8x12", "Supreme"];
  var i11 = ["10x8_Gloss", 5.90, 5.00, 4.50, "10x8", "Gloss"];
  var i12 = ["10x8_Lustre", 5.90, 5.00, 4.50, "10x8", "Lustre"];
  var i13 = ["10x8_Laminated", 8.90, 7.00, 4.20, "10x8", "Laminated"];
  var i14 = ["10x12_Gloss", 10.50, 9.00, 7.50, "10x12", "Gloss"];
  var i15 = ["10x12_Lustre", 10.50, 9.00, 7.50, "10x12", "Lustre"];
  var i16 = ["10x12.5_Gloss", 11.90, 9.50, 9.50, "10x12.5", "Gloss"];
  var i17 = ["10x12.5_Lustre", 11.90, 9.50, 9.50, "10x12.5", "Lustre"];
  var i18 = ["10x12.5_Laminated", 12.40, 10.00, 10.00, "10x12.5", "Laminated"];
  var i19 = ["10x15_Gloss", 14.50, 12.00, 10.75, "10x15", "Gloss"];
  var i20 = ["10x15_Lustre", 14.50, 12.00, 10.75, "10x15", "Lustre"];
  var i21 = ["12x8_Lustre", 6.00, 5.50, 4.50, "12x8", "Lustre"];
  var i21 = ["12x18_Lustre", 21.00, 21.00, 21.00, "12x8", "Lustre"];
  var i22 = ["6x20_Lustre", 18.00, 18.00, 18.00, "6x20", "Lustre"];
  var i23 = ["6x25_Lustre", 22.00, 22.00, 22.00, "6x25", "Lustre"];
  var i24 = ["10x20_Lustre", 23.00, 23.00, 23.00, "10x20", "Lustre"];
  var i25 = ["10x25_Lustre", 26.00, 26.00, 26.00, "10x25", "Lustre"];
  var i26 = ["12x20_Lustre", 25.00, 25.00, 25.00, "12x20", "Lustre"];
  var i27 = ["12x25_Lustre", 30.00, 30.00, 30.00, "12x25", "Lustre"];
  printPrices.push(i0);
  printPrices.push(i1);
  printPrices.push(i2);
  printPrices.push(i3);
  printPrices.push(i4);
  printPrices.push(i5);
  printPrices.push(i6);
  printPrices.push(i7);
  printPrices.push(i8);
  printPrices.push(i9);
  printPrices.push(i10);
  printPrices.push(i11);
  printPrices.push(i12);
  printPrices.push(i13);
  printPrices.push(i14);
  printPrices.push(i15);
  printPrices.push(i16);
  printPrices.push(i17);
  printPrices.push(i18);
  printPrices.push(i19);
  printPrices.push(i20);
  printPrices.push(i21);
  printPrices.push(i22);
  printPrices.push(i23);
  printPrices.push(i24);
  printPrices.push(i25);
  printPrices.push(i26);
  printPrices.push(i27);
  //
  // *******************************
  // Print order
  // ******************************* 
  // Get the image size cost and Quanitity
  export function getOrderSize(event) {
    // console.log("----------------------------");
    // console.log("onChange" + event.target);
    //get the size
    const p = (event.target.value);
    // get index of the size
    const i = sizefinish.selectedIndex;
    let pr = "0";
    // get the quantity
    const qty = printQty.value;
    // get the unit price
    if (qty < 11) {
      pr = printPrices[i][1];
    } else if (qty > 10 && qty < 31) {
      pr = printPrices[i][2];
    } else if (qty > 30) {
      pr = printPrices[i][3];
    }
    eachPrice.textContent = pr.toFixed(2);
    eachPrice.value = pr.toFixed(2);
    // get quantity x price & update the print price
    let price = Number(qty) * Number(pr);
    printPrice.value = price.toFixed(2);
    UpdateOrderTotals(event, price.toFixed(2));
    size.value = printPrices[i][4];
    finish.value = printPrices[i][5];

    let fileList = document.getElementById('filelist_1_list_div');
    let fileCount = fileList.childElementCount;
    // console.log( 'fileCount: ' + fileCount);
    files.value = fileCount;

    // console.log( 'Qty: ' + qty);
    let totalPrints2 = fileCount*qty;
    totalPrints.value = totalPrints2;
    // console.log( 'totalPrints: ' + totalPrints2);

    // console.log( 'pr: ' + pr);
    let totalPrintsCost2 = totalPrints2*pr;

    // console.log( 'totalPrintsCost: ' + totalPrintsCost2.toFixed(2));
    totalPrintsCost.value = totalPrintsCost2.toFixed(2);
    updateNumberWFUField(printCost, totalPrintsCost2);
    UpdateOrderTotals();

  };


  export function UpdateOrderTotals() {
    // console.log("start update ******************************");
    let pc = totalPrintsCost.value;
     // console.log("printCost: ", Number(pc).toFixed(2));
    updateNumberWFUField(printCost, pc);
    //get the current delivery method & cost
    let dm = deliveryMethodPrice.textContent;
    dm = (Number(dm)).toFixed(2);
     // console.log("dm: ", dm);
    //get the current rural delivery cost
    let rp = ruralPrice.textContent;
    rp = (Number(rp)).toFixed(2);
    // console.log("rp: ", rp);
    //get the current saturday delivery cost
    let sd = saturdayPrice.textContent;
    sd = (Number(sd)).toFixed(2);
    // console.log("sd: ", sd);
    // Total it all
    let total = Number(dm) + Number(rp) + Number(sd);
    total = (Number(total)).toFixed(2);
    deliveryCost.value = total;
    // Get GST
    let gstCost1 = (Number(total) + Number(pc)) * 0.15;
    // console.log("gst: ", gstCost1.toFixed(2));
    gstCost1 = (Number(gstCost1)).toFixed(2);
    updateNumberWFUField(gstCost, gstCost1);
    let cost = (Number(total) + Number(pc)) + Number(gstCost1);
    // console.log("Cost: ", cost.toFixed(2));
    updateNumberWFUField(totalCost, cost);
    // console.log("end update *****************************");
  }

  function updateWFUField(fieldName, newValue) {
    fieldName.value = newValue;
    // console.log("updateWFUField: ", fieldName.className, ", ", newValue);
  }

  function updateWFUText(fieldName, newValue) {
    fieldName.textContent = newValue.toFixed(2);
    // console.log("updateWFUText: ", fieldName.className, ", ", fieldName.textContent);
  }

  function updateNumberWFUField(fieldName, newValue) {
    // console.log("updateNumberWFUField: ", fieldName.className, ", ", newValue);
    let nn = Number(newValue).toFixed(2);
    fieldName.value = nn;
    // console.log("updateNumberWFUField: ", fieldName,  newValue, fieldName.textContent);
  }

  function updateNumberWFUField2(fieldName, newValue) {
    // console.log("updateNumberWFUField: ", fieldName.className, ", ", newValue);
    let nn = Number(newValue).toFixed(2);
    fieldName.textContent = nn;
    // console.log("updateNumberWFUField: ", fieldName,  newValue, fieldName.textContent);
  }

  function getWFUField(fieldName) {
    let fn = document.getElementById(fieldName);
    // console.log("get field name: ", fieldName, " ", fn);
    return fn;
  }