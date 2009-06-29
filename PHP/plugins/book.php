<?php

require_once("include/pluginUtils.php");

require_once("include/database.php");
//require_once("include/db-config.php");
//require_once("include/session.php");

addPlugin(new BookPlugin());

class BookPlugin
{
  public function search($query, $database)
  {
   $q = "SELECT * FROM ldb_books WHERE author LIKE \"%$query%\" OR "
       ."description LIKE \"%$query%\" OR title LIKE \"%$query%\""
       ." ORDER BY author";

   $results = mysql_query($q);
   if($results) {
 	   $numresults = mysql_num_rows($results);
   }

   if ($numresults == 0) {
//      echo "<p>Sorry, your search: &quot;".$trimmed."&quot; returned zero results</p>";
   } else {
	   if(empty($s)) {
	      $s = 0;
	   }

	   // get results
//           $q .= " limit $s,$limit";
	   $result = $database->query($q);

	   // begin to show results set
	   $count = 1 + $s ;
//                echo "<br /><table border='1'><tr>";

		$itemFields = array("ISBN", "Title", "Author", "Publisher", "ReleaseDate", "Rating", "Description");
//                for($i=0; $i<count($itemFields); $i++)
//                {
//                    //$field = mysql_fetch_field($result);
//                    echo "<td>$itemFields[$i]</td>";
//                }

	   while ($row = mysql_fetch_array($result)) {
             $resultslist[$index]["ISBN"] = $row["isbn"];
             $resultslist[$index]["Title"] = $row["title"];
             $resultslist[$index]["Author"] = $row["author"];
             $resultslist[$index]["Publisher"] = $row["publisher"];
             $resultslist[$index]["Release Date"] = $row["releasedate"];
             $resultslist[$index]["Rating"] = $row["rating"];
             $resultslist[$index]["Description"] = $row["description"];


//                        echo "<tr>";
//                        echo "<td>".$row["isbn"]."</td>";
 //                       echo "<td>".$row["title"]."</td>";
//                        echo "<td>".$row["author"]."</td>";
//                        echo "<td>".$row["publisher"]."</td>";
//                        echo "<td>".$row["releasedate"]."</td>";
//                        echo "<td>".$row["rating"]."</td>";
//                        echo "<td>".$row["description"]."</td>";
//                        echo "</tr>";
	      $count++ ;
              $index++;
	   }
//           echo "</table><br />";

            return $resultslist;
   } // if
  } // search
} // class

?>
