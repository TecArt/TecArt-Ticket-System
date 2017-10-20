	<div id="head">
		<div id="head-in">
			<h1><?php echo $this->translate['create_new']; ?></h1>
		</div>
	</div>

	<form action="<?php echo $this->baseUrl;?>?co=ticket/add_ticket" method="post">
	    <div id="datas">
			<div id="datas-in">
	           	<table class="form"> 
	              	<tr>
	                  	<td style="width:130px">
	                  		<?php echo $this->translate['name']; ?>: *
	                  	</td>
	                  	<td colspan="5">
		                  	<?php if (isset($p_name)) {
    ?>
		                        <input name="name" value ="<?php echo $p_name; ?>"type="text" size="30" maxlength="255" style="width:100%">
		                  	<?php
} else {
        ?>
		                        <input name="name" type="text" size="30" maxlength="255" style="width:100%">
		                  	<?php
    }?>      
	                  	</td>
	                </tr>
	                <tr>
		                <td>
		                	<?php echo $this->translate['email']; ?>: *
		                </td>
		                <td colspan="5">
			                <?php if (isset($p_email)) {
        ?>
			                       <input name="email" type="text" size="30" maxlength="255" value="<?php echo $p_email; ?>" style="width:100%;">
			                <?php
    } else {
        ?>
			                       <input name="email" type="text" size="30" maxlength="255" value="<?php echo $email; ?>" style="width:100%;">
			                <?php
    }?>
		                </td>
	                </tr>
                    <tr>
                        <td>
                        	<?php echo $this->translate['category']; ?>:
                       	</td>
                        <td>
                        	<select name="category">
                                <?php foreach ($categories_fields as $key=>$field) {
        if (isset($cate) && $cate === $key) {
            echo"<option value=".$key." selected>".$field;
        } else {
            echo"<option value=".$key.">".$field;
        }
    }?>
                        	</select>
                        </td>
                        <td style="width:120px">
                        	<?php echo $this->translate['priority']; ?>:
                       	</td>
                        <td>
                            <select name="priority">
	                            <?php foreach ($priorities_fields as $key=>$field) {
        if (isset($priority)) {
            if ($priority === $key) {
                echo"<option value=".$key." selected>".$field;
            } else {
                echo"<option value=".$key.">".$field;
            }
        } else {
            if ($key == 3) {
                echo"<option value=".$key." selected>".$field;
            } else {
                echo"<option value=".$key.">".$field;
            }
        }
    }?>
                            </select>  
                        </td>
                        <?php if (count($sections) > 1) {
        ?>
                        <td style="width:120px">
                        	<?php echo $this->translate['sections']; ?>:
                        </td>
                        <td>
                            <select class="search" name="section" size="1">
			                    <?php foreach ($sections as $id => $name) {
            ?>
			                        <?php if ($id == $selectedsection) {
                ?>
			                            <option selected value="<?php echo $id; ?>"><?php echo $name; ?></option>
			                        <?php
            } else {
                ?>
			                            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>                            
			                    <?php
            }
        } ?>                            
                            </select>
                        </td>      
                        <?php
    } elseif (count($sections) == 1) {
        ?>
                        <td><input type="hidden" name="section" value="<?php reset($sections);
        echo key($sections); ?>"></td>
                        <?php
    } else {
        ?>
                        <td><input type="hidden" name="section" value="0"></td>
                        <?php
    }?>                        
                    </tr>
	                <tr>
		                <td>
		                	<?php echo $this->translate['subject']; ?>: *
		                </td>
		                <td colspan="5">
			                <?php if (isset($subject)) {
        ?>
			                        <input name="subject" value="<?php echo $subject; ?>" type="text" size="60" maxlength="255" style="width:100%">
			                <?php
    } else {
        ?>
			                        <input name="subject" type="text" size="60" maxlength="255" style="width:100%;">
			                <?php
    }?>
		                </td>           
	                </tr>
	                <tr>
		                <td style="vertical-align:top">
		                	<?php echo $this->translate['description']; ?>: *
		                </td>
		                <td colspan="5">
			                <?php if (isset($notes)) {
        ?>
			                        <textarea name="notes" cols="50" rows="10" style="width:100%"><?php echo($notes); ?></textarea>
			                <?php
    } else {
        ?>
			                        <textarea name="notes" cols="50" rows="10" style="width:100%"></textarea>
			                <?php
    }?>
		                </td>
	                </tr>
	                <tr>
	                	<td>&nbsp;</td>
		                <td colspan="5"><input type="submit" value="<?php echo $this->translate['save']; ?>" class="button-ticket"></td>
	                </tr>
				</table>
			</div>
		</div>
    </form>