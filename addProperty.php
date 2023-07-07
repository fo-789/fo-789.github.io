<?php
    clearstatcache();
  $page_title = (isset($_GET['id'])) ? 'Update Property' : 'Add Property';
  // require_once("cliin/paheader.php");
  $id = 0;
  if(isset($_GET['id']) && $_GET['id']>0) {
    $id = mysqli_real_escape_string($connection, $_GET['id']);

    $query = $conn->prepare("SELECT * FROM properties WHERE id = ?");
    $query->execute([$id]);

    $row = $query->fetch(PDO::FETCH_ASSOC);
    $values = $row;
    $title = 'Update Property';
  } else {
    $default_values = array('p_type'=>'', 'p_status'=>'', 'p_price'=>'', 'p_annu'=>'', 'p_rooms'=>'', 'p_baths'=>'', 'p_kits'=> '', 'p_width'=>'', 'p_height'=>'', 'p_area'=>'', 'p_state'=>'', 'p_city'=>'', 'p_district'=>'', 'p_images'=>'', 'p_desc'=>'', 'p_features'=>'');
    $values = (isset($_SESSION['values'])) ? $_SESSION['values'] : $default_values;
    $title = 'Add Property';
  }

  if(!is_dir("../properties/1")) {
    mkdir("../properties/1", 0604);
  }
?>
      <style>
        iframe {
          margin-top: 15px
        }
      </style>
      <!-- Main -->
      <main class="main-container">
        <div class="main-title">
          <h2><?= $title?></h2>
        </div>

        <?php  
          showErrors();
          showSuccess();
        ?>
        <form action="addProperty" method="post" enctype="multipart/form-data">
          <input type="hidden" value="<?= $id ?>" name="id">


          <div class="reg-wrap">
            <div class="reg-header">
              <h2>Basic Info</h2>
            </div>

            <div class="reg-content p-plus">
              <div class="input-wrap flex">

                <div class="input-cn">
                    <label for="p_type">Property Type</label>
                    <select name="p_type" id="p_type">
                      <option value="0">Select Property Type</option>
                      <?php 
                        $p_types = array("1" => "House", "2" => "Apartment", "3" => "Land");
                        foreach($p_types as $v => $t) {
                          $selected = ($v == $values['p_type']) ? 'selected' : '';
                          echo "<option value='$v' $selected>$t</option>";
                        }
                      ?>
                    </select>
                </div>
      
                <div class="input-cn">
                  <label for="p_status">Property Status</label>
                  <select name="p_status" id="p_status" onchange="checkIfRent(this)">
                    <option value="0">Select Property Status</option>
                    <?php 
                      $p_statuss = array("1" => "Sale", "2" => "Rent", "3" => "Rented", "4" => "Sold");
                      foreach($p_statuss as $v => $t) {
                        $selected = ($v == $values['p_status']) ? 'selected' : '';
                        echo "<option value='$v' $selected>$t</option>";
                      }
                    ?>
                  </select>
                </div>
      
                <div class="input-cn">
                    <label for="p_price">Property Price (optional)</label>
                    <input type="number" name="p_price" value="<?= $values['p_price']?>" placeholder="Property Price ($)">
                </div>
      
                <div class="input-cn" style="display: none">
                  <label for="p_annu">Annuities</label>
                  <select name="p_annu" id="p_annu">
                    <?php 
                      $p_annus = array("weekly" => "Weekly", "monthly" => "Monthly", "yearly" => "Yearly");
                      foreach($p_annus as $v => $t) {
                        $selected = ($v == $values['p_annu']) ? 'selected' : '';
                        echo "<option value='$v' $selected>$t</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </div>


          <div class="reg-wrap">
            <div class="reg-header">
              <h2>Listing Info</h2>
            </div>
            <div class="reg-content p-plus">
              <div class="input-wrap flex">
      
                <div class="input-cn">
                    <label for="p_rooms">Rooms</label>
                    <input type="number" name="p_rooms" value="<?= $values['p_rooms']?>" placeholder="Rooms">
                </div>
      
                <div class="input-cn">
                    <label for="p_baths">Bathrooms</label>
                    <input type="number" name="p_baths" value="<?= $values['p_baths']?>" placeholder="Bathrooms">
                </div>
                <div class="input-cn">
                    <label for="p_kits">Kitchens</label>
                    <input type="number" name="p_kits" value="<?= $values['p_kits']?>" placeholder="Kitchens">
                </div>
      
                <div class="input-cn">
                    <label for="p_width">Meter Width</label>
                    <input type="number" name="p_width" value="<?= $values['p_width']?>" placeholder="Meter Width">
                </div>
      
                <div class="input-cn">
                    <label for="p_height">Meter Height</label>
                    <input type="number" name="p_height" value="<?= $values['p_height']?>" placeholder="Meter Height">
                </div>

              </div>
            </div>
          </div>


          <div class="reg-wrap">
            <div class="reg-header">
              <h2>Location</h2>
            </div>
            <div class="reg-content p-plus">
              <div class="input-wrap flex">
      
                <div class="input-cn">
                    <label for="p_state">State</label>
                    <select name="p_state" id="p_state">
                    <?php
                        $citiesq = $conn->prepare("SELECT id, state FROM states");
                        $citiesq->execute();
                        while($row = $citiesq->fetch(PDO::FETCH_ASSOC)) {
                        $s_id = $row['id'];
                        $state = $row['state'];
                        $selected = ($state == 'Puntland') ? 'selected' : '';
                        if($values['p_state'] != '') $selected = ($s_id == $values['p_state']) ? 'selected' : '';

                        echo "<option value='$s_id' $selected>$state</option>";
                        }
                    ?>
                    </select>
                </div>

                <div class="input-cn">
                    <label for="p_city">City</label>
                    <select name="p_city" id="p_city">
                    <?php
                        $citiesq = $conn->prepare("SELECT id, city FROM cities");
                        $citiesq->execute();
                        while($row = $citiesq->fetch(PDO::FETCH_ASSOC)) {
                        $c_id = $row['id'];
                        $city = $row['city'];
                        $selected = ($city == 'Galkayo') ? 'selected' : '';
                        if($values['p_city'] != '') $selected = ($c_id == $values['p_city']) ? 'selected' : '';
                        
                        echo "<option value='$c_id' $selected>$city</option>";
                        }
                    ?>
                    </select>
                </div>
      
                <div class="input-cn">
                    <label for="p_district">District</label>
                    <input type="text" name="p_district" value="<?= $values['p_district']?>" id="heightRef" placeholder="District">
                </div>

                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d501.34372533728975!2d46.63999229008605!3d24.687548125488902!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f1cfb13c3bb65%3A0x3fac91e0fadc535!2sAlAtlaal%20grills!5e0!3m2!1sen!2ssa!4v1686127410119!5m2!1sen!2ssa" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                
              </div>
            </div>
          </div>


          <div class="reg-wrap">
            <div class="reg-header">
              <h2>Images & Features</h2>
            </div>
            <div class="reg-content p-plus">
              <div class="input-wrap flex">
      
                <?php if($id > 0) { ?>
                <div class="input-cn" style=" display: flex; align-items: center; ">
                    <label for="p_images" style="font-size: 18px; font-weight: 700">Editing images is available in this <a href="properties">link</a></label>
                </div>
                <?php }else { ?>
                <div class="input-cn">
                    <label for="p_images">Property Visual <small>(optional)</small><small>(Please select the first two to be main,entrance)</small></label>
                    <input type="file" name="p_images[]"  multiple="multiple" placeholder="Property Visual">
                </div>
                <?php } ?>
      
                <div class="input-cn">
                    <label for="p_baths">Description (optional)</label>
                    <textarea name="p_desc" id="heightSet" placeholder="(Optional)" cols="30"><?= trim($values['p_desc'])?></textarea>
                </div>

                <div class="input-cn">
                  <label for="features">Amenities (optional)</label>
                  <input type="text" name="p_features" id="features">
                  <button class="btn btn-primary" type="button" onclick="addFeature()" style="float: left">Add Feature</button>
                </div>

                <div class="input-cn">
                  <ul id="featuresList">
                    <?php 
                      $pfar = explode(",", $values['p_features']);
                      foreach($pfar as $fe) {
                        if($fe != '') {
                          echo '
                            <li onclick="deleteFeature(this)">
                              <input type="hidden" name="features[]" value="'.$fe.'">'.$fe.'</input>
                            </li>
                          ';
                        }
                      }
                      
                    ?>
                  </ul>
                </div>

              </div>
            </div>
          </div>
              
            <div class="input-cn">
              <button class="btn btn-primary" type="submit" name="add_property"><?= $title?></button>
            </div>
          </div>
        </form>
      </main>
      <!-- End Main -->

    </div>

    <!-- Scripts -->
    <!-- ApexCharts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <!-- Custom JS -->
    <script src="ani.js"></script>
    <script>
      var p_annu = document.getElementById('p_annu');
      var p_stats = document.getElementById('p_status');
      var feature = document.getElementById('features');
      var featuresList = document.getElementById('featuresList');
      var mainHeight = document.getElementById('heightRef').getBoundingClientRect();
      document.getElementById('heightSet').style.height = mainHeight['height']+'px';

      function checkIfRent(cir) {
        if(cir.value == 2 || cir.value == 3) {
          p_annu.parentElement.style.display = 'block';
        } else {
          p_annu.parentElement.style.display = 'none';
        }
      }
      function addFeature() {
        if(feature.value != '') {
          newFeature = feature.value.charAt(0).toUpperCase() + feature.value.slice(1);
          var newli = document.createElement("li");
          newli.setAttribute("onclick","deleteFeature(this);");
          newli.innerHTML = '<input type="hidden" name="features[]" value="'+newFeature+'">'+newFeature+'</input>';
          feature.value = ''
          featuresList.prepend(newli)
        }
      }
      function deleteFeature(fea) {
        fea.remove();
      }

      document.onkeypress = function(e){
        if (!e) e = window.event;
        var keyCode = e.code || e.key;
        if (keyCode == 'Enter'){
          addFeature()
          return false;
        }
      }

      checkIfRent(p_stats)
    </script>
  </body>
</html>


<?php


  unset($_SESSION['errors']);
  unset($_SESSION['values']);
  unset($_SESSION['success']);
  if(isset($_POST['add_property'])){
      // Getting Values From Form
      $id = mysqli_real_escape_string($connection,$_POST['id']);
      $p_type = mysqli_real_escape_string($connection,$_POST['p_type']);
      $p_status = mysqli_real_escape_string($connection,$_POST['p_status']);
      $p_annu = mysqli_real_escape_string($connection,$_POST['p_annu']);
      $p_price = mysqli_real_escape_string($connection,$_POST['p_price']);
      $p_rooms = mysqli_real_escape_string($connection,$_POST['p_rooms']);
      $p_baths = mysqli_real_escape_string($connection,$_POST['p_baths']);
      $p_kits = mysqli_real_escape_string($connection,$_POST['p_kits']);
      $p_width = mysqli_real_escape_string($connection,$_POST['p_width']);
      $p_height = mysqli_real_escape_string($connection,$_POST['p_height']);
      $p_state = mysqli_real_escape_string($connection,$_POST['p_state']);
      $p_city = mysqli_real_escape_string($connection,$_POST['p_city']);
      $p_district = mysqli_real_escape_string($connection,$_POST['p_district']);
// echo $p_district; exit;
      
      $org_img_name = $_FILES['p_images']['name'];


      $p_desc = mysqli_real_escape_string($connection,$_POST['p_desc']);
      if(!empty($_POST['features'])) {
        $p_features = implode(", ", $_POST['features']);
      } else {
        $p_features = '';
      }
      $date = date("Y-m-d H:i:s");
      $flag = false;
      

      // Assigning Values if reload happens
      $vlaues = array();
      $values['p_type'] = $p_type;
      $values['p_status'] = $p_status;
      $values['p_annu'] = $p_annu;
      $values['p_price'] = $p_price;
      $values['p_rooms'] = $p_rooms;
      $values['p_baths'] = $p_baths;
      $values['p_kits'] = $p_kits;
      $values['p_width'] = $p_width;
      $values['p_height'] = $p_height;
      $values['p_state'] = $p_state;
      $values['p_city'] = $p_city;
      $values['p_district'] = $p_district;
      // $values['p_images'] = $p_images;
      $values['p_desc'] = $p_desc;
      // $values['p_features'] = $p_features;
      $errors = array();



      if($p_type == 0){
          $flag = true;
          $errors['p_type'] = "* Please select a valid type";
      }

      if($p_status == 0){
          $flag = true;
          $errors['p_status'] = "* Please select a valid offer status";
      }

      if(empty($p_price)){
          $flag = true;
          $errors['p_price'] = "* Please select a valid Price";
      }

      if(empty($p_width)){
          $flag = true;
          $errors['p_width'] = "* Please enter meter width";
      }

      if(empty($p_height)){
          $flag = true;
          $errors['p_height'] = "* Please enter meter height";
      }

      if(empty($p_district)){
          $flag = true;
          $errors['p_district'] = "* Please choose disctrict";
      }

      if($p_state == 0){
          $flag = true;
          $errors['p_state'] = "* Please enter State";
      }

      if($p_city == 0){
          $flag = true;
          $errors['p_city'] = "* Please enter City";
      }
      

      if($org_img_name[0] != '') {
        foreach($_FILES['p_images']['tmp_name'] as $key => $tmp_name ){
          $img_path = $_FILES['p_images']['tmp_name'][$key];
          $img_size = filesize($img_path);
          $img_info = finfo_open(FILEINFO_MIME_TYPE);
          $img_type = finfo_file($img_info, $img_path);
          $allowedTypes = [
             'image/png' => 'PNG',
             'image/png' => 'png',
             'image/jpeg' => 'jpg'
          ];           
          $extension = $allowedTypes[$img_type];
  
  
          if ($img_size === 0) {
              $flag = true;
              $errors['imgsizes'] = "*Image is empty";
          }
          
          if ($img_size > 2000000) {
              $flag = true;
              $errors['imgsizel'] = "*Your file is larger than 2Mb";
          }
          
          if(!in_array($img_type, array_keys($allowedTypes))) {
              $flag = true;
              $errors['imgtype'] = "*Your file could only be png or jpg";
          }
            ////////////////////////////////////////////////////////////////
        }
    }


      
      // exit;
      // if($id==0) {
        // if(empty($_POST['password']) || empty($_POST['confirm_password']))
        // {
        //     $flag = true;
        //     $errors['password'] = "* Please Confirm you password";
        // } else if($_POST['password'] != $_POST['confirm_password'])
        // {
        //     $flag = true;
        //     $errors['confirm_password'] = "* Passwords do not match";
        // }
      // }



     
      if($flag) {
          $_SESSION['errors'] = $errors;
          $_SESSION['values'] = $values;
          header("Location: addProperty".(($id > 0)?'?id='.$id:''));
          exit;
      } else {   
        
          $p_area = $p_width * $p_height;
          $db_values = array($p_type, $p_status, $p_annu, $p_price, $p_rooms, $p_baths, $p_kits, $p_width, $p_height, $p_area, $p_state, $p_city, $p_district, $p_desc, $p_features);
          $filessql = '';
          $filespara = '';
          

          // Check File Upload 
          if(file_exists($_FILES['p_images']['tmp_name'][0]) || is_uploaded_file($_FILES['p_images']['tmp_name'][0])) {
            if($id > 0){
              $filessql = ' `p_images` = 1,';
            } else {
              $filessql = '  `p_images`,';
              $filespara = ' 1,';
            } 
          }

          if($id > 0){
            $sql = "UPDATE properties SET `p_type` = ?, `p_status` = ?, `p_annu` = ?, `p_price` = ?, `p_rooms` = ?, `p_baths` = ?, `p_kits` = ?, `p_width` = ?, `p_height` = ?, `p_area` = ?, `p_state` = ?, `p_city` = ?, `p_district` = ?,$filessql `p_desc` = ?, `p_features` = ? WHERE id = ?";
            array_push($db_values, $id);
            $_SESSION['success'] = "Property Updated Succefully";
          } else {
            $sql = "INSERT INTO `properties` (`p_type`, `p_status`, `p_annu`, `p_price`, `p_rooms`, `p_baths`, `p_kits`, `p_width`, `p_height`, `p_area`, `p_state`, `p_city`, `p_district`,$filessql `p_desc`, `p_features`, `created_at`) ";
            $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,$filespara ?, ?, ?)";
            array_push($db_values, $date);
            $_SESSION['success'] = "Property Added Succefully";
          }  

          $insert = $conn->prepare($sql)->execute($db_values);
          $p_id = $conn->lastInsertId();

          if(file_exists($_FILES['p_images']['tmp_name'][0]) || is_uploaded_file($_FILES['p_images']['tmp_name'][0])) {
            // Upload
            $img_name = 1;
            $targetDirectory = "../properties/1/$p_id";
            mkdir("../properties/1/$p_id", 0604); 
            foreach($_FILES['p_images']['tmp_name'] as $key => $tmp_name ){
              $img_path = $_FILES['p_images']['tmp_name'][$key];
              $img_size = filesize($img_path);
              $img_info = finfo_open(FILEINFO_MIME_TYPE);
              $img_type = finfo_file($img_info, $img_path);
              $allowedTypes = [
                 'image/png' => 'png',
                 'image/jpeg' => 'jpg'
              ];           
              $extension = $allowedTypes[$img_type];
  
              $newFilepath = $targetDirectory . "/" . $img_name . "." . $extension;
              copy($img_path, $newFilepath);
              compressImage($newFilepath);
  
              unlink($img_path);

              $img_name++;
                ////////////////////////////////////////////////////////////////
            }
            exit;

          }


          if(!$insert){
              die("ERROR $sql");
          } else {
              header("Location: properties");
              exit;
          }


          // $mail = new PHPMailer();
          // $mail->isSMTP();
          // $mail->Host = "";
          // $mail->SMTPAuth = "true";
          // $mail->SMTPSecure = "tls";
          // $mail->port = "587";
          // //if($debug) $email->SMTPDebug = 2;
          // $mail->Username = ""; //Sender Email
          // $mail->Password = "";//Sender Email Password
          // $mail->Subject = "";
          // $mail->setFrom(""); //Sender Email
          // $mail->Body = '';
          // $mail->IsHTML(true); 
          // $mail->addAddress($email);
          // // Telling user hIs registration is successfull
          // if($mail->Send()){
          // }else{ 
          //     echo $mail->ErrorInfo;
          // }
      }
    }
?>
