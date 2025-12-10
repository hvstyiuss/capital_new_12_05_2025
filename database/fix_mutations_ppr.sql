-- SQL script to add ppr column to mutations table
-- Run this if you cannot run Laravel migrations

-- Check if column exists before adding
SET @dbname = DATABASE();
SET @tablename = "mutations";
SET @columnname = "ppr";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 'Column already exists.' AS result;",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " VARCHAR(255) NULL AFTER id;")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add foreign key constraint (if it doesn't exist)
-- Note: You may need to adjust the foreign key name based on your database
SET @fk_name = (SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = @dbname 
                AND TABLE_NAME = @tablename 
                AND COLUMN_NAME = @columnname 
                AND REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1);

SET @preparedStatement = (SELECT IF(
  @fk_name IS NOT NULL,
  "SELECT 'Foreign key already exists.' AS result;",
  CONCAT("ALTER TABLE ", @tablename, " ADD CONSTRAINT mutations_ppr_foreign FOREIGN KEY (ppr) REFERENCES users(ppr) ON DELETE CASCADE;")
));
PREPARE addFkIfNotExists FROM @preparedStatement;
EXECUTE addFkIfNotExists;
DEALLOCATE PREPARE addFkIfNotExists;






