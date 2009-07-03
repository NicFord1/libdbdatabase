<?php
// Allow us to use addPlugin to add ourselves to the global plugin list
require_once("include/pluginUtils.php");
// Allow us to issue queries on the database, i.e. to search for items and
// obtain their data
require_once("include/database.php");

// Whenever we are included by somebody, add an instance of ourselves to the
// global plugin list.
addPlugin(new DVDPlugin());

class DVDPlugin {
   public function search($query, $database) {
      // Issue query to database for rows matching query
      $q = "SELECT * FROM ldb_dvds WHERE title LIKE \"%$query%\" OR "
          ."author LIKE \"%$query%\" OR description LIKE \"%$query%\""
          ." ORDER BY title";

      $result = $database->query($q);

      // Place each match into next index of $resultlist. $resultlist should
      // only contain the columns we want the user to see. That's why we do
      // this selective copy of only some fields. The caller is going to put
      // the data from $resultslist into a table displayed to the user.
      $index = 0;
      $resultslist[] = array();
      while ($row = mysql_fetch_array($result)) {
      	 $resultslist[$index]["ITEMID"] = $row["itemid"];
         $resultslist[$index]["UPC"] = $row["upc"];
         $resultslist[$index]["Title"] = $row["title"];
         $resultslist[$index]["Director"] = $row["author"];
         $resultslist[$index]["Genre"] = $row["genre"];
         $resultslist[$index]["Studio"] = $row["publisher"];
         $resultslist[$index]["Release Date"] = $row["releasedate"];
         $resultslist[$index]["Rating"] = $row["rating"];
         $resultslist[$index]["Description"] = $row["description"];
         $index++;
      }
      if(count($resultslist)==0) return null;
      return $resultslist;
   } // search
      public function get($id, $database) {
      // Issue query to database for rows matching query
      $q = "SELECT * FROM ldb_dvds WHERE itemid = $id";

      $result = $database->query($q);

      // Place each match into next index of $resultlist. $resultlist should
      // only contain the columns we want the user to see. That's why we do
      // this selective copy of only some fields. The caller is going to put
      // the data from $resultslist into a table displayed to the user.
      $index = 0;
      $resultslist[] = array();
      while ($row = mysql_fetch_array($result)) {
      	 $resultslist[$index]["ITEMID"] = $row["itemid"];
         $resultslist[$index]["ISBN"] = $row["isbn"];
         $resultslist[$index]["Title"] = $row["title"];
         $resultslist[$index]["Author"] = $row["author"];
         $resultslist[$index]["Publisher"] = $row["publisher"];
         $resultslist[$index]["Release Date"] = $row["releasedate"];
         $resultslist[$index]["Rating"] = $row["rating"];
         $resultslist[$index]["Description"] = $row["description"];
         $index++;
      }
      return $resultslist;
   } // get   
   
} // class
?>