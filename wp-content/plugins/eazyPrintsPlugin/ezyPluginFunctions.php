<?php

function doSomethingElse()
{
  // echo '<div class="completeTheOrder">';
  // echo '<h3>' . "not complete" . '</h3>';
  // echo '</div>';
}

function function_alert($ORDER_NUMBER)
{
  // echo '<script>' .
  //   'confirm("$ORDER_NUMBER");' .
  //   '</script>';
}

function showUserDetails()
{
  $current_user = wp_get_current_user();
  acf_form(array(
    'post_id'       => $current_user,
    'form' => true,
    'form_attributes' => array(),
    'post_title'    => false,
    'field_groups' => array(2786),
    'fields' => array('first_name', 'last_name', 'email', 'address', 'suburb', 'city', 'country', 'postcode', 'phone'),
    'return' => add_query_arg('updated', 'true', get_permalink()),
    'submit_value'  => __('Update Profile')
  ));
}

function showCostDetails()
{
  $current_user = wp_get_current_user();
  acf_form(array(
    'post_id'       => $current_user,
    'form' => true,
    'form_attributes' => array(),
    'post_title'    => false,
    'field_groups' => array(2816),
    'fields' => array('print_cost', 'delivery_cost', 'subtotal', 'gst', 'total', 'complete_order',),
  ));
}

function showDeliveryDetails()
{
  $current_user = wp_get_current_user();
  acf_form(array(
    'post_id'       => $current_user,
    'form' => true,
    'form_attributes' => array(),
    'post_title'    => false,
    'field_groups' => array(2823),
    'fields' => array('delivery_method_and_details', 'rural_delivery', 'saturday_delivery', 'deliver_to_postal_address', 'postal_address', 'additional_instructions'),
    'return' => add_query_arg('updated', 'true', get_permalink()),
    'submit_value'  => __('Update Profile')
  ));
}


function drawOrderTable($ORDER_NUMBER)
{
  global $wpdb;
  $wpdb->show_errors();
  $totalPrice = 0;

  $orders = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT ID,order_number,userID,uploads
    FROM wp_ezyUploads
    WHERE order_number = %s",
      $ORDER_NUMBER
    )
  );


  echo '<div class="myDiv1">';
  echo '<h5>Order#: ', $ORDER_NUMBER . '</h5>';
  echo '<table>';
  echo '<thead>';
  echo '<tr class="wfu_browser_tr wfu_included wfu_visible wfu_row-1 wfu_browser-1">';
  echo '<th class="wfu_browser_td wfu_col-1 wfu_browser-1">' . 'User ID:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-1 wfu_browser-1">' . 'Order:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-2 wfu_browser-1">' . 'File Name:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-3 wfu_browser-1">' . 'File path:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-4 wfu_browser-1">' . 'Qty:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-5 wfu_browser-1">' . 'Size:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-6 wfu_browser-1">' . 'Resize:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-7 wfu_browser-1">' . 'Finish:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-8 wfu_browser-1">' . 'Price:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-9 wfu_browser-1">' . 'Total Price:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-10 wfu_browser-1">' . 'Date/time:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-4 wfu_browser-1">' . 'Status:' . '</th>';
  echo '<th class="wfu_browser_td wfu_col-4 wfu_browser-1">' . 'Edit:' . '</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  foreach ($orders as $order) {
    $uppy = json_decode($order->uploads);

    echo '<tr class="wfu_browser_tr wfu_included wfu_visible wfu_row-1 wfu_browser-1">';
    echo '<td class="wfu_browser_td wfu_col-2 wfu_browser-1">' . $order->userID . '</td>';
    echo '<td class="wfu_browser_td wfu_col-2 wfu_browser-1">' . $order->order_number . '</td>';
    echo '<td class="wfu_browser_td wfu_col-3 wfu_browser-1">' . $uppy->file_name . '</td>';
    echo '<td class="wfu_browser_td wfu_col-3 wfu_browser-1">' . $uppy->file_path . '</td>';
    echo '<td class="wfu_browser_td wfu_col-4 wfu_browser-1">' . $uppy->qty . '</td>';
    echo '<td class="wfu_browser_td wfu_col-5 wfu_browser-1">' . $uppy->size . '</td>';
    echo '<td class="wfu_browser_td wfu_col-6 wfu_browser-1">' . $uppy->resize . '</td>';
    echo '<td class="wfu_browser_td wfu_col-7 wfu_browser-1">' . $uppy->finish . '</td>';
    echo '<td class="wfu_browser_td wfu_col-8 wfu_browser-1">' . $uppy->price . '</td>';
    echo '<td class="wfu_browser_td wfu_col-9 wfu_browser-1">' . $uppy->total_price . '</td>';
    echo '<td class="wfu_browser_td wfu_col-10 wfu_browser-1">' . $uppy->date_time . '</td>';
    echo '<td class="wfu_browser_td wfu_col-4 wfu_browser-1">' . '</td>';
    echo '<td class="wfu_browser_td wfu_col-4 wfu_browser-1">' . '<a href="#">' . 'edit' . '</a>' . '</td>';
    echo '</tr>';
    $totalPrice = $totalPrice + $uppy->total_price;
  }
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
  echo '<td class="wfu_browser_td wfu_col-8 wfu_browser-1">' . '</td>';
  echo '<td id="totalPrintPrice" class="wfu_browser_td wfu_col-9 wfu_browser-1">' . number_format((float)$totalPrice, 2, '.', '') . '</td>';
  echo '<td class="wfu_browser_td wfu_col-10 wfu_browser-1">' . '</td>';
  echo '<td class="wfu_browser_td wfu_col-11 wfu_browser-1">' . '</td>';
  echo '<td class="wfu_browser_td wfu_col-12 wfu_browser-1">' . '</td>';
  echo '</tr>';
  echo '</tfoot>';
  echo '</table';
  echo '</div>';
  echo '</div>';
}


function completeTheOrder($ORDER_NUMBER)
{
  $current_user = wp_get_current_user();

  global $wpdb;

  $orders = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT ID,order_number,userID,uploads
    FROM wp_ezyUploads
    WHERE order_number = %s",
      $ORDER_NUMBER
    )
  );

  $items = [];

  foreach ($orders as $order) {
    $uppy = json_decode($order->uploads);
    $items[] = $uppy;
  }

  $upload3 = json_encode($items, JSON_PRETTY_PRINT);

  $thisUser = array(
    'first_name' => get_field('first_name', $current_user),
    'last_name' => get_field('last_name', $current_user),
    'email' => get_field('email', $current_user),
    'address' => get_field('address', $current_user),
    'suburb' => get_field('suburb', $current_user),
    'city' => get_field('city', $current_user),
    'postcode' => get_field('postcode', $current_user),
    'phone' => get_field('phone', $current_user),

    'delivery_details' => get_field('delivery_method_and_details', $current_user),
    'rural_delivery' => get_field('rural_delivery', $current_user),
    'saturday_delivery' => get_field('saturday_delivery', $current_user),
    'deliver_to_postal_address' => get_field('deliver_to_postal_address', $current_user),
    'postal_address' => get_field('postal_address', $current_user),
    'additional_instructions' => get_field('additional_instructions', $current_user)

  );
  $upload = json_encode($thisUser, JSON_PRETTY_PRINT);
  $costs = array(
    'print_cost' => get_field('print_cost', $current_user),
    'delivery_cost' => get_field('delivery_cost', $current_user),
    'subtotal' => get_field('subtotal', $current_user),
    'gst' => get_field('gst', $current_user),
    'total' => get_field('total', $current_user)
  );
  $upload1 = json_encode($costs, JSON_PRETTY_PRINT);

  $wpdb->insert(
    $wpdb->prefix . 'ezy_orders', // name of the table
    array( // 'key' => 'value'
      'order_number' => $ORDER_NUMBER,
      'user' => get_current_user_id(),
      'date' => date('Y-m-d H:i:s'),
      'order_status' => "tba2",
      'costs' => $upload1,
      'user_details' => $upload,
      'items' => $upload3
    ),
    array(
      "%d", // $ORDER_NUMBER,
      '%d', // 'userID',
      '%s', // 'status'
      '%s', // 'costs'
      '%s', // 'user details'
      '%s' // 'items'
    )
  );
  // $ORDER_NUMBER = get_option('ORDER_NUMBER');
  // $ORDER_NUMBER++;
  // update_option('ORDER_NUMBER', $ORDER_NUMBER);
}
