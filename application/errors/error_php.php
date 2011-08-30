<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4><?php echo $error->getHeading(); ?></h4>

<p>Severity: <?php echo $error->getSeverity(); ?></p>
<p>Message:  <?php echo $error->getMessages('', ''); ?></p>
<p>Filename: <?php echo $error->getFile(); ?></p>
<p>Line Number: <?php echo $error->getLine(); ?></p>

</div>
