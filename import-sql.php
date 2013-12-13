<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:twitter="http://api.twitter.com">
  <channel>
    <title>Twitter / aucun_lien</title>
    <link>http://twitter.com/aucun_lien</link>
    <atom:link type="application/rss+xml" href="https://twitter.com/statuses/user_timeline/aucun_lien.rss" rel="self"/>
    <description>Twitter updates from Aucun Lien / aucun_lien.</description>
    <language>en-us</language>
    <ttl>40</ttl>
	<?php

	$dsn = 'mysql:dbname=AL_thinkup;host=localhost';
	$user = 'root';
	$password = 'root';

	try {
		$dbh = new PDO($dsn, $user, $password);		
	} catch (PDOException $e) {
		echo 'Connexion échouée : ' . $e->getMessage();
	}

	$query = "SELECT * FROM `tu_posts` WHERE network='twitter' AND author_username='aucun_lien' AND post_id != 124507346880823296 ORDER BY post_id DESC LIMIT 500 OFFSET 0";
    
    foreach  ($dbh->query($query) as $row) { ?>
		<item>
			<title><?=$row["post_text"]?></title>
			<description><?=$row["post_text"]?></description>
			<pubDate><?=$row["pub_date"]?></pubDate>
			<guid>http://twitter.com/aucun_lien/statuses/<?=$row["post_id"]?></guid>
			<link>http://twitter.com/aucun_lien/statuses/<?=$row["post_id"]?></link>
			<twitter:source><?=$row["source"]?></twitter:source>
			<twitter:place/>
		</item>
	<?php } ?>
  </channel>
</rss>