Biotea Website

This code is the biotea website.  It includes the front-end HTML documents,
and the API application.

Copyright (c) FSU 2012

TODO:
-------------------------------------------------------------------------------
* Need to finish refactoring the indexer so that it actually works...
  - Will manually add items to the database instead of relying on the EntityManager->persist()
    to do it for us, since that causes so many problems.
  - The EntityManager can still handle retrieval and schema definition, since that works well, just
    not insertions for now.
