<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css"
		rel="stylesheet">
	<style>
		.mt40 {
			margin-top: 40px;
		}
	</style>
</head>

<body>

	<div class="container">
		
		<div class="row mt40">
			<div class="col-md-10">
				<h2><?php echo $title; ?></h2>
			</div>
			<div class="col-md-2">
				<a href="<?php echo base_url('create'); ?>" class="btn btn-danger">Add Note</a>
			</div>
			<br><br>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Id</th>
						<th>Title</th>
						<th>Description</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($notes): ?>
						<?php foreach ($notes as $note): ?>
							<tr>
								<td><?php echo $note->id; ?></td>
								<td><?php echo $note->title; ?></td>
								<td><?php echo $note->description; ?></td>
								<td>
									<a href="<?php echo base_url('note/edit/' . $note->id); ?>" class="btn btn-primary">Edit</a>
									<a href="<?php echo base_url('note/delete/' . $note->id); ?>"
										class="btn btn-danger">Delete</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<a href="<?php echo base_url('note/logout'); ?>">Logout</a>
	</div>

</body>

</html>
