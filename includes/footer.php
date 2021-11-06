</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= $actual_link ?>/js/bootstrap.min.js"></script>
<script src="<?= $actual_link ?>/js/jquery.bootstrap-growl.min.js"></script>
<script src="<?= $actual_link ?>/js/app.boubimsp.min.js?v=<?= $javascript_version ?>"></script>
<script src="<?= $actual_link ?>/js/lightbox.min.js"></script>
</body>
</html>
<?php if(isset($_SESSION['flash'])) { ?>
<script type="text/javascript">
<?php foreach ($_SESSION['flash'] as $type => $message) { ?>
$(document).ready(function(){$.bootstrapGrowl('<?= $message ?>',{type:"<?= $type ?>",width:"auto",allow_dismiss:!1})});
<?php } ?>
</script>
<?php unset($_SESSION['flash']); } ?>