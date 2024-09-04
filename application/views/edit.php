<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Codeigniter CRUD Application With Example - Tutsmake.com</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
    <style>
        .mt40{
            margin-top: 40px;
        }
    </style>
</head>
<body>

<div class="container">

<div class="row">
    <div class="col-lg-12 mt40">
        <div class="pull-left">
            <h2>Edit Note</h2>
        </div>
    </div>
</div>


<form action="<?php echo base_url('note/store') ?>" method="POST" name="edit_note">
   <input type="hidden" name="id" value="<?php echo $note->id ?>">
     <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Title</strong>
                <input type="text" name="title" class="form-control" value="<?php echo $note->title ?>" placeholder="Enter Title">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Description</strong>
                <textarea class="form-control" col="4" name="description"
                 placeholder="Enter Description"><?php echo $note->description ?></textarea>
            </div>
        </div>
        <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>

</div>

</body>
</html>
