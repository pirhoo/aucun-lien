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

      // Open the file
      $file = fopen("inc/data/aucun_lien_backup-2012-09-15.csv", "r");
      // To skip the first line
      fgetcsv($file, 0, ",");      
      // Fetch every line of the file
      for($iterator = 0; $line = fgetcsv($file, 0, ",") ; $iterator++) { 

        //if($iterator > 50) break;

        $text = $line[2];
        $date = new DateTime($line[0]." ".$line[1]);
        $guid = $line[3];

        if( count($line) > 4 ) {
          $text .= " ".$line[3];
          $guid = $line[4];
        }

        
        //$text = htmlentities($text, ENT_QUOTES); // PROBLEME D'ENCODAGE
        $text = str_replace('\n', "\n", $text);        
        //$text = "Pirhoo";

        ?>
        <item>
          <title><![CDATA[<?php echo $text; ?>]]></title>
          <description><![CDATA[<?php echo nl2br($text); ?>]]></description>
          <pubDate><?=$date->format('D, d M Y H:i:s')?> GMT</pubDate>
          <guid><?=$guid?></guid>
          <link><?=$guid?></link>
        </item>

     <?php }

     echo $iterator;

    ?>
  </channel>
</rss>