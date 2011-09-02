<?
/* 
 * The idea here is to load and process customer records using scripts. This
 * differs from normal displaying of customer records in much the same way as a
 * mail merge works.
 *
 * A script is a series of statements or questions that the call centre staff
 * member will read as they respond to a customer.  This way you can have staff
 * that are working simultaneously on multiple projects.  It can be useful for
 * outbound marketing as well as inbound calls as it allows for an easy way to
 * store information.
 *
 * A script is based on the following MySQL structure:
 *
 * id - autoincrement index (automatically created)
 * name - name of the script
 * description - description of the script
 * owner - the id of the user who created this script
 * lastupdated - automatically updated timestamp for last update
 * groupid - the id of a group of people allowed access to this script
 *
 * You then have script entries which are stored in the script_entries table:
 *
 * id - autoincrement index (automatically created)
 * script_id - id of the script that this refers to
 * type - the type of script entry i.e.:
 *       -1 - end of section/page
 *        0 - statement followed by text field
 *        1 - statement followed by a yes/no field
 *        2 - statement followed by a combo box field
 *        3 - statement followed by nothing
 * statement - the text to display
 * order - the position of the entry in the script as a whole
 *
 * If you are using a combo box field, the choices are supplied from the
 * script_choices table.  This table has the following structure:
 *
 * id - autoincrement index (automatically created)
 * script_entry_id - id of the script entry that this refers to
 * text - the text of the choice (i.e. 'apple' or 'pear')
 *
 */

if (isset($_GET['add_section'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "INSERT INTO script_entries (`script_id`, `type`, `statement`, `order`) VALUES (".sanitize($_POST['script_id']).",".sanitize($_POST['type']).",".sanitize($_POST['statement']).",".sanitize($_POST['order']).")";
    //echo $sql;
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $new_id = mysqli_insert_id($connection);
    echo $new_id;
    exit(0);
}

if (isset($_GET['add_combobox_entry'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "INSERT INTO script_choices (`script_entry_id`, `text`) VALUES (".sanitize($_POST['script_entry_id']).",".sanitize($_POST['text']).")";
    //echo $sql;
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    //  $new_id = mysqli_insert_id($connection);
    //echo $new_id;
    exit(0);
}

if (isset($_GET['delete_section'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "DELETE FROM script_entries WHERE id = ".sanitize($_GET['delete_section']);
    echo $sql;
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    exit(0);
}
if (isset($_GET['save_field'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $url = parse_url($_POST['url']);
    $exploded = explode("=",$url['query']);
    $type = $exploded[0];
    $id = sanitize($exploded[1]);
    $field = sanitize($_POST['id'], false);
    $value = sanitize($_POST['new_value']);
    switch ($type) {
        case "edit":
            $sql = "UPDATE scripts SET $field = $value WHERE id = $id";
            $result = mysqli_query($connection, $sql);
            break;
        default:
            break;
    }
    
    $response['is_error'] = false;
    $response['error_string'] = mysqli_error($connection);;
    $response['html'] = $_POST['new_value'];
    echo json_encode($response);
    exit(0);
}


require "header.php";
if (isset($_GET['add'])) {
    ?>
    <br />
    <form action="scripts.php?edit_new=1" method="post">
    <table class="sample">
    <tr>
    <th>Script Name</th>
    <td><input type="text" name="name"></td>
    </tr>
    <tr>
    <th colspan="2">Description</th>
    </tr>
    <tr>
    <td colspan="2"><textarea name="description" rows="5"></textarea></td>
    </tr>
    <tr>
    <th colspan="2"><center><input type="submit" value="Add Script" style="width: 200px"></center></th>
    </tr>
    </table>
    </form>
    <?
    require "footer.php";
    exit(0);
}
if (isset($_GET['edit_new'])) {
    //TODO: make groupid work
    $result = mysqli_query($connection, "INSERT INTO scripts (name, description, owner, lastupdated, groupid) VALUES (".sanitize($_POST['name']).",".sanitize($_POST['description']).", ".sanitize($_SESSION['user_name']).",NOW(),0)") or die(mysqli_error($connection));
    $id =  mysqli_insert_id($connection);
    redirect("scripts.php?edit=$id",0);
    exit(0);
}
if (isset($_GET['edit'])) {
    $result = mysqli_query($connection, "SELECT * FROM scripts WHERE id = ".sanitize($_GET['edit']));
    if (mysqli_num_rows($result) == 0) {
        // Not found - shouldn't be possible
    } else {        
        $row = mysqli_fetch_assoc($result);
        ?>
        <script language="javascript">
        var entries_to_ids=new Array();
        var counter = 0;
        function addInput(divName){
            counter++;
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "Entry <br><input type='text' name='myInputs[]'>";
            document.getElementById(divName).appendChild(newdiv);
        }
        function add_end_of_section(divName){
            counter++;
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "<div class='script_input_entry' id='entry"+counter+"'><a href='#' onclick='delete_entry("+counter+");'><img src='images/delete.png' alt='Delete' width='16 height='16' align='right'></a><hr /></div>";
            document.getElementById(divName).appendChild(newdiv);
        }
        
        function save_end_of_section(){
            new Ajax.Request('scripts.php?add_section=1',{parameters: {script_id: <?=$_GET['edit']?>, type: -1, statement: "", order: counter}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             entries_to_ids[counter] = parseInt(response);
                             }
                             }
                             });
            
        }
        
        function delete_entry_from_database(item) {
            //alert("Deleting item "+item+" from script <?=$_GET['edit']?> (id "+entries_to_ids[parseInt(item)]+")");
            new Ajax.Request('scripts.php?delete_section='+entries_to_ids[parseInt(item)]);
        }
        
        function delete_entry(item) {
            /* The item number is the number in the script starting from one - bearing in mind that there may be deleted
             entries.  I.E. Item 1 may not be id 1.  If you had three entries and you delete id 1 and id 2 then item 1 would
             be id 2 (id is zero based) */
            Dialog.confirm('Are you sure you want to remove this section?', {className:'alphacube', width:400, 
                           okLabel: 'Yes', cancelLabel: 'No',
                           onOk:function(win){
                           jQuery("#entry"+item).remove();
                           delete_entry_from_database(item);
                           return true;
                           }
                           }
                           );
        }
        
        
        /* Statement followed by text field */
        
        function save_statement_followed_by_text_field(statement, divName){
            new Ajax.Request('scripts.php?add_section=1',{parameters: {script_id: <?=$_GET['edit']?>, type: 0, statement: statement, order: counter}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             entries_to_ids[counter] = parseInt(response);
                             }
                             }
                             });
        }
        
        function add_statement_followed_by_text_field(statement, divName){
            counter++;
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "<div class='script_input_entry' id='entry"+counter+"'><a href='#' onclick='delete_entry("+counter+");'><img src='images/delete.png' alt='Delete' width='16' height='16' align='right'></a>"+nl2br(statement)+" <br><input type='text' name='field"+counter+"'></div>";
            document.getElementById(divName).appendChild(newdiv);
        }
        
        
        /* Statement followed by yes/no */
        
        function save_statement_followed_by_yesno(statement, divName){
            new Ajax.Request('scripts.php?add_section=1',{parameters: {script_id: <?=$_GET['edit']?>, type: 1, statement: statement, order: counter}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             entries_to_ids[counter] = parseInt(response);
                             }
                             }
                             });
            
        }
        
        function add_statement_followed_by_yesno(statement, divName){
            counter++;
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "<div class='script_input_entry' id='entry"+counter+"'><a href='#' onclick='delete_entry("+counter+");'><img src='images/delete.png' alt='Delete' width='16' height='16' align='right'></a>"+nl2br(statement)+" <br><select name='field"+counter+"'><option value='YES'>Yes</option><option value='NO'>No</option></select></div>";
            document.getElementById(divName).appendChild(newdiv);
        }
        
        /* Statement followed by combobox */
        
        function save_statement_followed_by_combobox(statement, comboboxes, divName){
            var saved_id = 0;
            new Ajax.Request('scripts.php?add_section=1',{parameters: {script_id: <?=$_GET['edit']?>, type: 2, statement: statement, order: counter}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             //alert("Response: "+response);
                             entries_to_ids[counter] = parseInt(response);
                             saved_id = parseInt(response);
                             //alert(saved_id);
                             comboboxes.each(function(){
                                             new Ajax.Request('scripts.php?add_combobox_entry=1',{parameters: {script_entry_id: saved_id, text: jQuery(this).val()}, onSuccess: function(transport){
                                                              if (transport.responseText) {
                                                              //alert(transport.responseText);
                                                              
                                                              }
                                                              }
                                                              });  
                                             });
                             
                             }
                             }
                             });       
        }
        
        function add_statement_followed_by_combobox(statement, comboboxes, divName){
            counter++;
            var newdiv = document.createElement('div');
            var ih = "<div class='script_input_entry' id='entry"+counter+"'><a href='#' onclick='delete_entry("+counter+");'><img src='images/delete.png' alt='Delete' width='16' height='16' align='right'></a>"+nl2br(statement)+" <br><select name='field"+counter+"'>";
            comboboxes.each(function(){
                            ih += "<option value='"+jQuery(this).val()+"'>"+jQuery(this).val()+"</option>";
                            });
            ih += "</select></div>";
            newdiv.innerHTML = ih;            
            document.getElementById(divName).appendChild(newdiv);
        }
        
        function add_statement_followed_by_combobox_from_db(statement, comboboxes, divName){
            counter++;
            var newdiv = document.createElement('div');
            var ih = "<div class='script_input_entry' id='entry"+counter+"'><a href='#' onclick='delete_entry("+counter+");'><img src='images/delete.png' alt='Delete' width='16' height='16' align='right'></a>"+nl2br(statement)+" <br><select name='field"+counter+"'>";
            comboboxes.forEach(function(item){
                               ih += "<option value='"+item+"'>"+item+"</option>";
                               });
            ih += "</select></div>";
            newdiv.innerHTML = ih;            
            document.getElementById(divName).appendChild(newdiv);
        }
        
        
        function add_combobox_option(divName){
            //alert("Adding option");
            var newdiv = document.createElement('div');
            newdiv.innerHTML = '<input type="text" name="combobox_option[]"><br />';
            document.getElementById(divName).appendChild(newdiv);
            Windows.focusedWindow.updateHeight();
        }
        
        
        
        
        /* Statement followed by nothing */
        
        function save_statement_followed_by_nothing(statement, divName){
            new Ajax.Request('scripts.php?add_section=1',{parameters: {script_id: <?=$_GET['edit']?>, type: 3, statement: statement, order: counter}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             entries_to_ids[counter] = parseInt(response);
                             }
                             }
                             });
            
        }
        
        function add_statement_followed_by_nothing(statement, divName){
            counter++;
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "<div class='script_input_entry' id='entry"+counter+"'><a href='#' onclick='delete_entry("+counter+");'><img src='images/delete.png' alt='Delete' width='16' height='16' align='right'></a>"+nl2br(statement)+"</div>";
            document.getElementById(divName).appendChild(newdiv);
        }
        
        
        
        function nl2br(dataStr) {
            return dataStr.replace(/(\r\n|\r|\n)/g, "<br />");
        }
        function display_adder() {
            /*
             *       -1 - end of section/page
             *        0 - statement followed by text field
             *        1 - statement followed by a yes/no field
             *        2 - statement followed by a combo box field
             *        3 - statement followed by nothing
             */
            switch (jQuery("#input_type option:selected").val()) {
                case '-1':
                    /* End of section/page */
                    save_end_of_section('dynamicInput');
                    add_end_of_section('dynamicInput');
                    break;
                case '0':
                    Dialog.confirm('Statement: <textarea id="statement_text" rows="10"></textarea>', {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   save_statement_followed_by_text_field(nl2br(jQuery('#statement_text').val()), 'dynamicInput');
                                   add_statement_followed_by_text_field(jQuery('#statement_text').val(), 'dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                case '1':
                    Dialog.confirm('Statement: <textarea id="statement_text" rows="10"></textarea>', {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   save_statement_followed_by_yesno(nl2br(jQuery('#statement_text').val()), 'dynamicInput');
                                   add_statement_followed_by_yesno(jQuery('#statement_text').val(), 'dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                case '2':
                    /* Add a statement followed by a combobox */
                    Dialog.confirm('Statement: <textarea id="statement_text" rows="10"></textarea><div id="combobox_options">Options: </div><a href="#" onclick="add_combobox_option(\'combobox_options\');">Add Option</a>', {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   save_statement_followed_by_combobox(nl2br(jQuery('#statement_text').val()), jQuery('input[name="combobox_option[]"]'), 'dynamicInput');
                                   add_statement_followed_by_combobox(jQuery('#statement_text').val(), jQuery('input[name="combobox_option[]"]'), 'dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                case '3':
                    Dialog.confirm('Statement: <textarea id="statement_text" rows="10"></textarea>', {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   save_statement_followed_by_nothing(nl2br(jQuery('#statement_text').val()), 'dynamicInput');
                                   add_statement_followed_by_nothing(jQuery('#statement_text').val(), 'dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                default:
                    break;
                    
            }
            
            
        }
        </script>
        <br />
        <table class="sample" width="400">
        <tr>
        <th>Script Name</th>
        <td><img src="images/pencil.png" align="right"><div id="name"><?if (strlen(stripslashes($row['name'])) > 0) {echo stripslashes($row['name']);} else {echo "No description";}?></div></td>
        </tr>
        <tr>
        <th>Description</th>
        <td><img src="images/pencil.png" align="right"><div id="description"><?if (strlen(stripslashes($row['description'])) > 0) {echo stripslashes($row['description']);} else {echo "No description";}?></div></td>
        </tr>
        </table>
        <br />
        <select name="input_type" id="input_type">
        <option value="0">statement followed by text field</option>
        <option value="1">statement followed by a yes/no field</option>
        <option value="2">statement followed by a combo box field</option>
        <option value="3">statement followed by nothing</option>
        <option value="-1">end of section/page</option>
        </select>
        
        <a href="#" onClick="display_adder();">Add Section</a>
        <br />
        <br />
        <input type="button" value="Close Script" onclick="window.location='scripts.php';">
        <br />
        <br />
        
        <div id="dynamicInput" class="script_input_section" style="">
        <center><h3>Sample Script</h3></center>
        <?
        $result_entries = mysqli_query($connection, "SELECT * FROM script_entries WHERE script_id = ".$row['id']);
        $x = 0;
        if (mysqli_num_rows($result_entries) > 0) {
            while ($row_entries = mysqli_fetch_assoc($result_entries)) {
                $x++;
                ?>
                <script language="javascript">
                entries_to_ids[<?=$x?>] = <?=$row_entries['id']?>;
                </script>
                <?
                switch ($row_entries['type']) {
                    case 0:
                        ?>
                        <script>add_statement_followed_by_text_field(<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput');</script>
                        <?
                        break;
                    case 1:
                        ?>
                        <script>add_statement_followed_by_yesno(<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput');</script>
                        <?
                        break;
                    case 2:
                        ?>
                        <script>
                        var combobox_entries=new Array();
                        <?
                        $sql = "SELECT * FROM script_choices WHERE script_entry_id = ".$row_entries['id'];
                        $result_comboboxes = mysqli_query($connection, $sql);
                        if (mysqli_num_rows($result_comboboxes) > 0) {
                            //echo "alert('$sql');";
                            $count = 0;
                            while ($row_comboboxes = mysqli_fetch_assoc($result_comboboxes)) {
                                $string = 'combobox_entries['.$count.'] = "'.$row_comboboxes['text'].'";';
                                echo $string;
                                $count++;
                            }
                        } else {
                            //echo "alert('No results from $sql');";
                        }
                        ?>
                        add_statement_followed_by_combobox_from_db(<?=stripslashes(sanitize($row_entries['statement']))?>, combobox_entries, 'dynamicInput');
                        </script>
                        <?
                        break;
                    case 3:
                        ?>
                        <script>add_statement_followed_by_nothing(<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput');</script>
                        <?
                        break;
                    case -1:
                        ?>
                        <script>add_end_of_section('dynamicInput');</script>
                        <?
                        break;
                }
                
            }
        }
        
        ?>
        </div>
        <?/*
           <table class="sample">
           <tr>
           <td>Bla</td>
           </tr>
           </table>
           */?>
        
        
        <div id="new_section_1" style="display:none">
        Bla        
        <div style="clear:both"></div>
        </div>
        
        <div id="new_section_2" style="display:none">
        Bla        
        <div style="clear:both"></div>
        </div>
        
        <div id="new_section_3" style="display:none">
        Bla        
        <div style="clear:both"></div>
        </div>
        
        <script>
        jQuery( "#name" ).eip( "scripts.php?save_field=1" );
        jQuery( "#description" ).eip( "scripts.php?save_field=1" );
        
        
        
        
        </script>
        <?
        
    }
    require "footer.php";
    exit(0);
}

/* Just display entries */
echo "<br />";
$result = mysqli_query($connection, "SELECT * FROM scripts");
if (mysqli_num_rows($result) == 0) {
    echo "There are currently no scripts defined.";
} else {
    echo '<table class="sample">';
    echo '<tr><th>Name</th><th>Last Updated</th><th>Delete</th></tr>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo '<td><a href="scripts.php?edit='.$row['id'].'">'.$row['name'].'&nbsp;<img src="images/pencil.png" alt="edit"></a></td><td>'.$row['lastupdated'].'</td><td><a href="scripts.php?delete='.$row['id'].'"><img src="images/delete.png" alt="delete"></a></td>';
        //print_pre($row);
        echo "</tr>";
    }
    echo '</table>';
}
require "footer.php";

?>