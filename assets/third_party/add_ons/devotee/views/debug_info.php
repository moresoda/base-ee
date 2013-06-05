<html>
<head>
	<title>Site Debug Information</title>
	<style type="text/css" >
	p { font-family: "Courier New", Courier, monospace; font-size: 18px; line-height: 24px;}
	</style>
</head>
<body>

<p>
	<strong>General Information</strong><br /><br />
	ExpressionEngine : <?php echo $ee_version;  ?><br />
	PHP : <?php echo $php_version; ?><br />
	DB Driver : <?php echo $db_driver; ?><br />
	Server Software : <?php echo $server_software; ?><br />
	Browser : <?php echo $browser; ?><br />
</p>

<p>
	<strong>Session Settings</strong><br /><br />
	User Session Type : <?php echo $user_session_type; ?><br />
	Admin Session Type : <?php echo $user_session_type; ?><br />
	Cookie Domain : <?php echo $cookie_domain; ?><br />
	Cookie Path : <?php echo $cookie_path; ?><br />
	CP Cookie Domain : <?php echo $cp_cookie_domain; ?><br />
	CP Cookie Path : <?php echo $cp_cookie_path; ?><br />
	CP Session TTL : <?php echo $cp_session_ttl; ?><br />
</p>

<p>
	<strong>Installed Add-ons</strong><br /><br />
<?php foreach($updates as $update): ?>
	
		<a href="<?php echo $update->devotee_link; ?>" ><?php echo $update->name; ?></a> <?php echo $update->version ?>
		<?php  
			if ( $update->update_available ) {
				echo "(current: {$update->current_version})";
			}
		?>
		[ <?php foreach($update->types as $abbr => $flag) {
			if ( $flag ) {
				echo $abbr.' ';
			}
		}
		?>]
	<br />
<?php endforeach; ?>
</p>

</body>
</html>