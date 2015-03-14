<p align='right'>Charles Crookes<br />
Drew Bergman<br />
Ivan Tewell<br />
Nicholas Ford</p>
# <p align='center'>Abstract<p></h1>

Our team has decided to create a library application, which will be written in PHP and connect to a MySQL database. The project is called <i><a href='http://LibDBDatabase.Nicks-Net.us'>LibDBDatabase</a></i>, which will be implemented as a library where users can add, remove, and find items. Items in the library consist of books, catalogs, peer-reviewed journals, novels, music CDs, educational and entertainment DVDs, and other media supported by a plugin system. Items in the database will have a weight attributed with them that represents its ratings. When a customer has an item for check-out or searches for an item, the library will make a recommendation to him or her based on ratings and relevance of other similar items.<br>
<br>
The application will implement several criteria listed below (<a href='FunctionalRequirements.md'>FunctionalRequirements</a>):<br>
<br>
<ul><li>A Search function, where one can search the library database using various criteria (subject, data, author, publisher, publish date, rating, price) to find a specific book or a series of books matching the query.</li></ul>

<ul><li>Several usertypes, which consists of admin (database managers), teller (sales person), and customer (adult, child, and senior citizen).</li></ul>

<ul><li>Abilities of the three usertypes defined below:<br>
<ol><li><b>Admin</b> has the ability to add, remove, and search items in the database.<br>
</li><li><b>Teller</b> has the ability to search items in the database and check in/out items to a user.<br>
</li><li><b>Customer</b> has the ability to search the database.</li></ol></li></ul>

<ul><li>Item recommendations will be offered. Items may be recommended based on the subject, data, author, publisher, publish date, ratings, price, and user reviews.</li></ul>

<ul><li>Items will be categorized in the database by various criterions mentioned above.</li></ul>

<ul><li>The library database will have a GUI that allows users to easily perform the available actions as appropriate to their usertype.