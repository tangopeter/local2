function testy2() {
   if (document.getElementById("filelist_2_filelabel_label1")) {
     const f1 = document.getElementById('filelist_2_filelabel_label1').title;
     chooseFinish(f1);
     console.log("f1: ",f1);
   }
 }

 function chooseFinish(filename) {
   console.log("filename: ", filename);


//  console.log("testy: ", theFile);
 // document.getElementById('userdata_1_field_0').value = theUser.jobNumber;
 // 'jobNumber' => get_field('job_number'),
 // 'size' => $filepath.$changable_data['user_data'][7]['value'],
 // 'finish' => $filepath.$changable_data['user_data'][8]['value'],
 // 'quantity' => $filepath.$changable_data['user_data'][3]['value'],
 // 'reSize' => $filepath.$changable_data['user_data'][4]['value']

   // var field = acf.getField('field_63866e3d26755');
   // // get the field instance
   // if (field.val()) {
   //   console.log("field: ", field.val("pinky") );
   // }
   // // show error if no value
   // else if (!field.val()) {
   //  console.log ('Please add a value');
   // }
 }