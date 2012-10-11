Biotea Website

This code is the biotea website.  It includes the front-end HTML documents,
and the API application.

Copyright (c) FSU 2012

TODO:
-------------------------------------------------------------------------------
- Fix Models so that they can be persisted via Doctrine ORM when we get that installed
  (ensure that their interfaces continue to function correctly)
  * This includes setting up a new "Vocabulary" model and changing the Topic interface around
- Install Doctrine when composer is working, and setup an Entity Manager in bootstrap.php
  * See: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html (in 10 quick steps)
- Setup model relationships and persisted values via Annotations and refactor the indexer
  * Perhaps we don't need the MySQL_Client class any more, just need to pass in the EM to the indexer? We'll see
  * Hopefully this will speed things up exponentially
- Viola - Use the EM in controllers to get things from the database.