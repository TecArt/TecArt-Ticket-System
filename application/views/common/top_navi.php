<div id="top">
	<div id="head">
		<div id="head-in">
			<h1><?php echo $company.' - '.$login_nr; ?></h1>
		</div>
        <div id="right">
            <div class="user-pass">
            	<a href='<?php echo $this->baseUrl; ?>?co=auth/change_password'><?php echo $this->translate['change_password']; ?></a>
           	</div>
        </div>
	</div>
	<div id="toolbar">
		<?php if (isset($type) && $type == 'current') {
    ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=current' class="aktuell-ticket-active"><?php echo $this->translate['show_current']; ?></a>
		<?php
} else {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=current' class="aktuell-ticket"><?php echo $this->translate['show_current']; ?></a>
		<?php
    }?>

		<?php if (isset($type) && $type == 'all') {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=all' class="all-ticket-active"><?php echo $this->translate['show_all']; ?></a>
		<?php
    } else {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=all' class="all-ticket"><?php echo $this->translate['show_all']; ?></a>
		<?php
    }?>

		<?php if (isset($action) && $action == 'new_ticket') {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/new_ticket' class="add-ticket-active"><?php echo $this->translate['create_new']; ?></a>
		<?php
    } else {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/new_ticket' class="add-ticket"><?php echo $this->translate['create_new']; ?></a>
		<?php
    }?>

		<?php if (isset($type) && $type == 'closed') {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=closed' class="closed-ticket-active"><?php echo $this->translate['closed_ticket']; ?></a>
		<?php
    } else {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=closed' class="closed-ticket"><?php echo $this->translate['closed_ticket']; ?></a>
		<?php
    }?>

		<?php if (isset($type) && $type == 'open') {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=open' class="new-ticket-active"><?php echo $this->translate['not_handle_ticket']; ?></a>
		<?php
    } else {
        ?>
			<a href='<?php echo $this->baseUrl; ?>?co=ticket/show&type=open' class="new-ticket"><?php echo $this->translate['not_handle_ticket']; ?></a>
		<?php
    }?>			
		
		<?php if (isset($partners_enabled) && $partners_enabled) {
        ?>
			<?php if (isset($action) && $action == 'partners') {
            ?>
				<a href='<?php echo $this->baseUrl; ?>?co=partners/show' class="partners-active"><?php echo $this->translate['partner_portal']; ?></a>
			<?php
        } else {
            ?>
				<a href='<?php echo $this->baseUrl; ?>?co=partners/show' class="partners"><?php echo $this->translate['partner_portal']; ?></a>
			<?php
        } ?>
			
			<?php if (isset($action) && $action == 'wishlist') {
            ?>
				<a href='<?php echo $this->baseUrl; ?>?co=partners/wishlist' class="wishlist-active"><?php echo $this->translate['partner_wishlist']; ?></a>
			<?php
        } else {
            ?>
				<a href='<?php echo $this->baseUrl; ?>?co=partners/wishlist' class="wishlist"><?php echo $this->translate['partner_wishlist']; ?></a>
			<?php
        } ?>
		<?php
    } ?>
		
		<a href='<?php echo $this->baseUrl; ?>?co=auth/logout' class="logout"><?php echo $this->translate['logout']; ?></a>

	</div>
