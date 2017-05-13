<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<link href="style.css" rel="stylesheet" />
<title>Blog Ecrivain</title>
</head>

<body>
<header>
<h1>Blog Ecrivain</h1>
</header>


			<?php foreach ($posts as $post): //Add all post?>
			<article>
				<h2><?php echo $post['title'] . ' <span class="date_size">' . $post['p_date'] . '</span>' ?></h2>
				<p><?php echo $post['content'] ?></p>
			</article>
			<?php endforeach ?>
			
			<footer class="footer">
				<a href="https://github.com/nasreddinebr/Projet.git">Blog Ecrivain</a> et le projet numéro 3 pour la formation Chef de projet Multimedia - Dev sur OpenClassRooms.
			</footer>
		
	</body>
</html>