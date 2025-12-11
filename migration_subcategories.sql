-- Migration script for subcategories feature
-- Creates subcategories table and updates articles table

-- Create subcategories table
DROP TABLE IF EXISTS subcategories;
CREATE TABLE subcategories
(
  id              smallint unsigned NOT NULL auto_increment,
  name            varchar(255) NOT NULL,
  categoryId      smallint unsigned NOT NULL,
  
  PRIMARY KEY     (id),
  FOREIGN KEY     (categoryId) REFERENCES categories(id) ON DELETE CASCADE
);

-- Add subcategoryId column to articles table
ALTER TABLE articles ADD COLUMN subcategoryId smallint unsigned NULL;
ALTER TABLE articles ADD FOREIGN KEY (subcategoryId) REFERENCES subcategories(id) ON DELETE SET NULL;

