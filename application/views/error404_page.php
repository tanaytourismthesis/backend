<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Tanay Tourism">
  <meta name="author" content="Tanay Tourism">

	<meta http-equiv='cache-control' content='no-cache, no-store, must-revalidate'>
	<meta http-equiv='expires' content='0'>
	<meta http-equiv='pragma' content='no-cache'>

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-theme.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/ripple.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/font-awesome/css/font-awesome.css'); ?>">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/style.css?tm='.date('mdYHisA')); ?>">

	<?php echo $this->template->meta; ?>

  <title><?php echo $this->template->title; ?></title>

	<script>
		var baseurl = "<?php echo base_url(); ?>";
	</script>
</head>
<body>
	<div class="error-page container">
		<?php echo $this->template->content; ?>
	</div>
</body>

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>

<?php echo $this->template->javascript; ?>

<?php echo $this->template->stylesheet; ?>
</html>
