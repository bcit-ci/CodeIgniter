<!DOCTYPE html>
<html lang="en">  
<head>
<title>Event Registration</title>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link href="<?php echo base_url(); ?>assets/css/style.css?7678232" rel='stylesheet' type='text/css' />

<!--load jquery-->
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php $this->view('shared/header'); ?>
<div class="container">
    <div class="row">
		<?php foreach($events as $event): ?>
        <div class="col-md-4">
           <div class="single-blog-item">
                    <div class="blog-thumnail">
                        <a href=""><img src="<?php echo base_url('assets/files/event/'. $event['event_image']);?>" alt="event-img"></a>
                    </div>
                    <div class="blog-content">
                        <h4><a href="event/{event_id}"><?php print $event['event_title']; ?></a></h4>
                        <p><?php print $event['event_description']; ?></p>
                        <a href="event/<?php print $event['event_id']; ?>" class="more-btn">View More</a>
                    </div>
                    <span class="blog-date"><?php print date('M d, Y',strtotime($event['event_starttime'])); ?></span>
            </div>
         </div>
		 <?php endforeach; ?>
    </div>
</div>
<?php $this->view('shared/footer'); ?>
</body>
</html>