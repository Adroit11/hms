<!-- LIBS -->
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/tablekit.js"></script>
<!-- ENDLIBS -->
<script>
function hilite(elem)
{
	elem.style.background = '#FFC';
}

function lowlite(elem)
{
	elem.style.background = '';
}
</script>
<!--</head>-->

<body>
<div class="mainbar">
         <?php
          $total_count = DB::GetOne("SELECT COUNT(*) AS total_count FROM patient where patient.id_lang = 2");   
          $pagination = new Pagination();
          $pagination->total_items = $total_count[0];
          $pagination->mid_range = 3;
          $pagination->paginate();
          echo "<br />Συνολικές Χρεώσεις Ασθενών : <b>$total_count[0]</b> <br /><br /><br />";
          echo $pagination->displayPages();
          echo "<span class=\"\">&nbsp;&nbsp;".$pagination->displayJumpMenu()."&nbsp;&nbsp;".
          $pagination->displayItemsPerPage()."</span>";
          ?>
        <div id="patient_div" class="article">
          <p></p>
          <br />
           <table id="patient" class="sortable resizable editable">
				<thead>
					<tr>
                    <!--<th class="nosort nocol noedit" id="action">Delete Record</th>-->
                    <th class="sortfirstdesc sortcol sortasc" id="firstName">Όνομα Ασθενή</th>
                    <th class="sortcol" id="lastName">Επώνυμο</th>
                    <th class="sortcol" id="registrationDate">Ημερ/νία Εγγραφής</th>
                    <th class="sortcol" id="id_doctor">Ιατρός</th>
                    <th class="sortcol" id="charges">Χρεώσεις</th>
                    </tr>
			</thead>
				<tfoot>
					<tr>
                    <!--<td>Delete Record</td>-->
                    <td>Όνομα Ασθενή</td>
                    <td>Επώνυμο</td>
                    <td>Ημερ/νία Εγγραφής</td>
                    <td>Ιατρός</td>
                    <td>Χρεώσεις</td>
                    </tr>
				</tfoot>
                
				<tbody id="patient_body">
                  
                  <?php
                  if($total_count[0] > 0) {
                  $stmt = DB::getInstance()->query("SELECT * FROM patient LEFT OUTER JOIN doctor on patient.id_doctor = doctor.id_doctor where patient.id_lang = 2 order by id_patient asc ".$pagination->limit);                                   
                  $i=1;
                  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) : 
                  ?>
                    <tr class="<?php echo ($i % 2 == 0) ? 'rowodd' : 'roweven'; ?>" id="<?php echo $row['id_patient']; ?>" onMouseOver="hilite(this);" onMouseOut="lowlite(this);">
                    <!--<td>
                    <img src="images/delete.gif" />
                    <a class="delete" href="#" onclick="deleteRecord(<?php echo $row['id_patient']; ?>); return false">delete</a>
                    </td>-->
                    <td><?php echo $row['firstName']; ?></td>
                    <td><?php echo $row['lastName']; ?></td>
                    <td><?php echo $row['registrationDate']; ?></td>
                    <td><?php echo $row['doctorName']." ".$row['doctorSurname']; ?></td>
                    <td><?php echo $row['charges']; ?></td>
                    </tr>
                  <?php
                  $i++;
                  endwhile;
                  }
                  else
                  {
                    echo "<tr><td colspan='4' class='nocol'>
                    <font color='red'>Όλες οι χρεώσεις των ασθενών διεγράφησαν</font>
                    </td></tr>";
                  }
                  DB::Close();
                  ?>
                  <?php
                  $stmtDoc = DB::getInstance()->query("SELECT * FROM doctor where doctor.id_lang = 2 order by id_doctor asc");
                  $doctors = "";
                  while($result = $stmtDoc->fetch(PDO::FETCH_ASSOC))
                  {
                    $doctors .= "['$result[doctorName] $result[doctorSurname]',";
                    $doctors .= "'$result[id_doctor]'],";
                  }
                  DB::Close();
                  ?>

               </tbody>
			</table><br />
			<div class="panel" id="doco-editable"> </div>						
		</div>
	
		<script type="text/javascript">
		
			TableKit.options.editAjaxURI = 'includes/transaction.php';
            
            TableKit.Editable.selectInput('id_doctor', {}, [
						<?php echo $doctors; ?>																												
					]);
            
		</script>
          <p>
<script type="text/javascript" charset="utf-8">
  var users_table = new TableKit( 'patient', {
    editAjaxURI: 'includes/transaction.php'
  });
  
/*  function addUser() {
    var timestamp = new Date().getTime();
    var new_table = 'patient' + timestamp;
    var url = 'transaction/addPatient' + '?timestamp=' + timestamp;
    new Ajax.Updater( 'article', url, { 
      asynchronous: true,
      onComplete: function() {
        new TableKit( new_table, {
          editAjaxURI: 'mainbar.php'
        })
      }
    });
  }*/
  
  function deleteRecord( id_record ) {
    var timestamp = new Date().getTime();
    var new_table = 'patient' + timestamp;
    var url = 'includes/transaction.php' + '?id_delete=' + id_record + '&table_delete=patient' + '&timestamp=' + timestamp;
    var answer = confirm('Are you sure you want to delete this patient?');
    if (answer) {
      new Ajax.Updater( 'patient_div', url, { 
        asynchronous: true,
        onComplete: function() {
          new TableKit( new_table, {
            editAjaxURI: 'includes/transaction.php'
          })
        }
      });
    }
  }
</script>
           <?php
           echo $pagination->displayPages();
           echo "<p class=\"paginate\">Σελίδα: <b>$pagination->current_page</b> από <b>$pagination->num_pages</b></p>\n"; 
          ?>
        </div>
      </div>