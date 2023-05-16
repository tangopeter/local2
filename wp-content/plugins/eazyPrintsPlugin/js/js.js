console.log("js  +", document.title  );



import { orderPage, getDeliveryMethod , checkRuralDelivery , checkSatDelivery , getOrderSize , UpdateOrderTotals } from './orderPage.js';
// import { testy2 } from './testy.js';
import { orderPage2 } from './orderPage2.js';
import { completeMyOrder } from './completeOrder.js';

// console.log("js " + document.title);
// jQuery(document).ready(function ($) {
//   if (typeof acf !== 'undefined') {
//     console.log('ACF is defined', acf);
//   }
//   else {
//     console.log('ACF not defined', acf);
//   }
// });

if (document.title == 'Order Prints – Ezy Prints') {
  console.log("4 "+ document.title);
  orderPage2();
}
else if (document.title == 'testy – Ezy Prints') {
  console.log("5 " + document.title);
  // orderPage2();
}
else if (document.title = 'Complete Order – Ezy Prints') {
  // console.log("7 " + document.title);
  completeMyOrder();
}
else if (document.title = 'My account – Ezy Prints') {
  completeThisOrder();
}

else if (document.title != 'Order Prints – Ezy Prints') {
  console.log("6 "+ document.title);
}
