<?php
// Allow us to use addPlugin to add ourselves to the global plugin list
require_once("include/pluginUtils.php");
// Allow us to issue queries on the database, i.e. to search for items and
// obtain their data
require_once("include/database.php");

// Whenever we are included by somebody, add an instance of ourselves to the
// global plugin list.
addPlugin(new PeriodicalPlugin());

class PeriodicalPlugin {
   public function search($query, $database) {
      // Issue query to database for rows matching query
      $q = "SELECT * FROM ldb_periodicals WHERE title LIKE \"%$query%\" OR "
          ."editor LIKE \"%$query%\" OR publisher LIKE \"%$query%\" OR "
          ."description LIKE \"%$query%\""
          ." ORDER BY title";

      $result = $database->query($q);

      // Place each match into next index of $resultlist. $resultlist should
      // only contain the columns we want the user to see. That's why we do
      // this selective copy of only some fields. The caller is going to put
      // the data from $resultslist into a table displayed to the user.
      $index = 0;
      $resultslist[] = array();
      while ($row = mysql_fetch_array($result)) {
         $resultslist[$index]["ISBN"] = $row["isbn"];
         $resultslist[$index]["ISSN"] = $row["issn"];
         $resultslist[$index]["SICI"] = $row["sici"];
         $resultslist[$index]["Title"] = $row["title"];
         $resultslist[$index]["Editor"] = $row["editor"];
         $resultslist[$index]["Genre"] = $row["genre"];
         $resultslist[$index]["Publisher"] = $row["publisher"];
         $resultslist[$index]["Release Date"] = $row["releasedate"];
         $resultslist[$index]["Rating"] = $row["rating"];
         $resultslist[$index]["Description"] = $row["description"];
         $index++;
      }
      return $resultslist;
   } // search
} // class
?>