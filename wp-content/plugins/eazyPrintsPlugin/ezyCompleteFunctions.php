<?php

function finalizeButton()
{
?>
  <div class="myDiv2">
    <input type=button id="completeOrder" class="button1" name="btn-comp" value='Finalize the Order'>
  </div>
<?php
}


function drawAccountOrderTable($ORDER_NUMBER)
{
  echo '<div class="mydiv1">';

  $USER = get_current_user_id();
  echo '<h5>User#: ', $USER . '</h5>';
  global $wpdb;
  $wpdb->show_errors();
  $totalPrice = 0;

  $users = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT ID,order_number,user, date, order_status, costs, user_details, items
  FROM wp_ezy_orders
  WHERE user = %s",
      $USER
    )
  );
  echo '<div class="myData3">';

  foreach ($users as $user) :
    $uppy = json_decode($user->items);

    echo '<pre>' . var_dump($user) . '</pre>';

    $costs = $user->costs;
    $costs = json_decode($user->costs);

    echo 'Print Cost: ' . $costs->print_cost . '<br/>';
    echo 'Delivery Cost: ' . $costs->delivery_cost . '<br/>';
    echo 'Subtotal: ' . $costs->subtotal . '<br/>';
    echo '+ GST: ' .  $costs->gst . '<br/>';
    echo 'Total: ' .  $costs->total . '<br/>';







    echo '<div class="mydata2">';

    echo '<table>';
    echo '<thead>';
    echo '<tr class="wfu_browser_tr wfu_included wfu_visible wfu_row-1 wfu_browser-1">';
    echo '<th class="wfu_browser_td wfu_col-1 wfu_browser-1">' . 'ID:' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-1 wfu_browser-1">' . 'Order:' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-2 wfu_browser-1">' . 'Date:' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-3 wfu_browser-1">' . 'Order status:' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-5 wfu_browser-1">' . 'User details:' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-6 wfu_browser-1">' . 'delivery:' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-7 wfu_browser-1">' . 'items' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-4 wfu_browser-1">' . 'Costs:' . '</th>';
    echo '<th class="wfu_browser_td wfu_col-4 wfu_browser-1">' . 'Edit:' . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    echo '<h5>Order#: ', $user->order_number . '</h5>';
    echo '<tr class="wfu_browser_tr wfu_included wfu_visible wfu_row-1 wfu_browser-1">';
    echo '<td class="wfu_browser_td wfu_col-2 wfu_browser-1">' . $user->ID . '</td>';
    echo '<td class="wfu_browser_td wfu_col-2 wfu_browser-1">' . $user->order_number . '</td>';
    echo '<td class="wfu_browser_td wfu_col-3 wfu_browser-1">' . $user->date . '</td>';
    echo '<td class="wfu_browser_td wfu_col-3 wfu_browser-1">' . $user->order_status . '</td>';


    echo '<td class="wfu_browser_td wfu_col-5 wfu_browser-1">' . $user->user_details . '</td>';
    echo '<td class="wfu_browser_td wfu_col-6 wfu_browser-1">' . $user->delivery_details . '</td>';
    echo '<td class="wfu_browser_td wfu_col-7 wfu_browser-1">' . $user->items . '</td>';
    echo '<td class="wfu_browser_td wfu_col-4 wfu_browser-1">' .
      'Print Cost: $<strong>' . $costs->print_cost . '</strong><br/>' .
      'Delivery Cost: $<strong>'  . $costs->delivery_cost . '</strong><br/>' .
      'Subtotal: $<strong>'  . $costs->subtotal . '</strong><br/>' .
      '+ GST:  $<strong>'  .  $costs->gst . '</strong><br/>' .
      'Total:  $<strong>'  .  $costs->total . '</strong><br/>' .
      '<br/>' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-4 wfu_browser-1">' . '<a href="#">' . 'edit' . '</a>' . '</td>';
    // echo '<td class="wfu_browser_td wfu_col-4 wfu_browser-1">' . '</td>';
    echo '</tr>';
    $totalPrice = $totalPrice + $uppy->total_price;

    echo '</tbody>';
    echo '<tfoot>';
    echo '<tr class="wfu_browser_tr wfu_included wfu_visible wfu_row-1 wfu_browser-1">';
    echo '<td class="wfu_browser_td wfu_col-1 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-2 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-3 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-4 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-5 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-6 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-7 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-8 wfu_browser-1">' . '</td>';
    echo '<td id="totalPrintPrice" class="wfu_browser_td wfu_col-9 wfu_browser-1">' . number_format((float)$totalPrice, 2, '.', '') . '</td>';
    echo '<td class="wfu_browser_td wfu_col-8 wfu_browser-1">' . '</td>';
    // echo '<td class="wfu_browser_td wfu_col-10 wfu_browser-1">' . '</td>';
    // echo '<td class="wfu_browser_td wfu_col-11 wfu_browser-1">' . '</td>';
    // echo '<td class="wfu_browser_td wfu_col-12 wfu_browser-1">' . '</td>';
    echo '</tr>';
    echo '</tfoot>';
    echo '</table';
    echo '</div>';
    echo '</div>';
    echo '</div>';
  endforeach;
}
