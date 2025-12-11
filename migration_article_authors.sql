-- Migration script for many-to-many relationship between articles and users (authors)
-- Creates junction table for article_authors

-- Create article_authors junction table
DROP TABLE IF EXISTS article_authors;
CREATE TABLE article_authors
(
  articleId    smallint unsigned NOT NULL,
  userId       int NOT NULL,
  
  PRIMARY KEY  (articleId, userId),
  FOREIGN KEY  (articleId) REFERENCES articles(id) ON DELETE CASCADE,
  FOREIGN KEY  (userId) REFERENCES users(id) ON DELETE CASCADE
);

