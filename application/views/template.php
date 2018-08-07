<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="True Money">
    <meta name="author" content="True Money">
	
	<meta http-equiv='cache-control' content='no-cache, no-store, must-revalidate'>
	<meta http-equiv='expires' content='0'>
	<meta http-equiv='pragma' content='no-cache'>
	
	<?php echo $this->template->meta; ?>

    <title><?php echo $this->template->title; ?></title>
	
	<script>
		var baseurl = "<?php echo base_url(); ?>";
	</script>
</head>
<body>
	<?php 
		echo $this->template->content;
	?>
</body>
<?php echo $this->template->javascript; ?>

<?php echo $this->template->stylesheet; ?>
</html>