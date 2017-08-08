Hello and thank you for viewing the API behind ICANHAZCODINGCHALLENGE.COM
PLEASE DO NOT STRESS TEST THE LIVE WEBSITE ICANHAZCODINGCHALLENGE.COM. I don't want to increase traffic too much on that server.


**ENDPOINTS**

There are 2 paths that can be called with this API

1) /create accepts a POST request with a parameter:
   url - the url that you are shortening
2) /read accepts a GET request with the following parameters:
   - url - this is the URL that you are trying to get stats on. I know that the same URL can be added multiple times, so this method is getting ALL click counts for the URL.
         it does not care if the short codes for the URL are the same.
   - code - can you can also pass the short code to get more information about that url's stats
   (if you leave code or URL blank, it gets broad data about all of the URLs in the system)
   - range - this param accepts the values "day", and "week". If you leave it blank, it gets all time
   - page - in order to account for millions of urls the api only outputs 10 at a time. 
   
In order to view it in action, just go to icanhazcodingchallenge.com/{code}




**BUILDING, RUNNING**

The app is written in PHP and uses a MySQL database. 
- In order to run the app you must have PHP and a MySQL database installed with a database called cloudflare.
- I have included an export of the db structure but you will need to change the db config in database.php

**ASSUMPTIONS**
- I did not include routes to delete or update the urls because it specifically said these are permanent urls
- I did not build any models classes for the URL "object" because it's such a simple app. Normally I would construct an object for each db table and some sort
  of entity manager that would allow my CRUD functions to be more useful. For example: $entityManager->findUrlByCode($code),
  instead of using plain sql everywhere.
- I assumed that all of the data in the DB did not need to come with one call, so I created calls to get basic information for all of the links
  and other calls to get more information about a specific link, such as stats.
- I decided to make table to store clicks rather than perhaps a serialized array in the link table because this way the data can easily be queried
  with MySQL calls
  



**DISCLAIMER**

Please do not judge the frontend that I created to interact with the app. I was running out of time, but wanted to still have
some sort of a frontend.  The app is completely decoupled from the frontend so that anyone can interact with it / build their own frontend.
I have enabled CORS and I am not requiring an access token so please don't be malicious ;)
  
