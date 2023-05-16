// $wpdb->query(
// $wpdb->prepare(
// "INSERT INTO $wpdb->orders ( order_number, user, file_name,file_path, qty, size,finish,resize, price, total_price)
// VALUES ( %d, %s, %s, %s, %s, %d, %s, %s, , %s, %d, %d )",
// $orderArray
// )
// );


// showStuff('myORDER_NUMBER');

// $orderArray = [];
// $orderArray[] = get_option('ORDER_NUMBER');
// $orderArray[] = $user;
// $orderArray[] = $additional_data["files"][0]["original_filename"];
// $orderArray[] = $additional_data["files"][0]["filepath"];
// $orderArray[] = $additional_data["files"][0]['user_data'][3]['value']; // qty
// $orderArray[] = $additional_data["files"][0]['user_data'][7]['value']; // size
// $orderArray[] = $additional_data["files"][0]['user_data'][8]['value']; // finish
// $orderArray[] = $additional_data["files"][0]['user_data'][5]['value']; // $each
// $orderArray[] = $additional_data["files"][0]['user_data'][6]['value']; // $total

// $wpdb->insert(
// $wpdb->prefix . 'orders',
// array(
// 'order_number' => $orderArray[0],
// 'user' => $orderArray[1],
// 'file_name' => $orderArray[2],
// 'file_path' => $orderArray[3],
// 'qty' => $orderArray[4],
// 'size' => $orderArray[5],
// 'finish' => $orderArray[6],
// 'resize' => $orderArray[7],
// 'price ' => $orderArray[8]
// // 'total_price' => $orderArray[9]
// ),
// array(
// '%d',
// '%s',
// '%s',
// '%s',
// '%s',
// '%d',
// '%s',
// '%s',
// '%s',
// '%d',
// '%d'
// )
// );



// // $wpdb->query(
// // $wpdb->prepare(
// // "INSERT INTO $wpdb->orders ( order_number, user, file_name,file_path, qty, size,finish,resize, price, total_price)
// // VALUES ( %d, %s, %s, %s, %s, %d, %s, %s, , %s, %d, %d )",
// // $orderArray
// // )
// // );

// <script>
  //     const thisJobNumber = document.getElementById("userdata_2_field_1");
  //     orderNumber = thisJobNumber.value;
  //     orderNumber++;
  //     console.log(orderNumber);
  //     thisJobNumber.value = orderNumber;
  //   
</script>