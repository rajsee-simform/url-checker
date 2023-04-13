# URL Checker

This is a class or Symfony service designed to check if a given URL already exists in a MySQL table with the InnoDB engine, Dynamic row format, and the utf8mb4 character set.

## Prerequisites

To use the `URLChecker` class or service, you will need to have the following software installed on your system:

- PHP 8.1 or higher
- Ctype PHP extension
- iconv PHP extension
- PCRE PHP extension
- Session PHP extension
- SimpleXML PHP extension
- Tokenizer PHP extension

These PHP extensions are installed and enabled by default in most PHP 8 installations.


## Problem Statement

We have a table with millions of URLs. We have an online form to insert new URLs. Before inserting new URLs, we want to check if they are already in the table so we don’t insert duplicates and we can inform the user that some or all the URLs already exist.

We have 3 constraints:

1. We can’t have the user wait for more than a second, so we need to know in near real-time if these URLs are in the table or not. What is the usual approach?

2. The URLs can be up to 2048 characters. What problem are we facing? How to solve that problem?

3. We want all versions of the same URL to match. For instance, if 2 URLs differ only by the scheme, they are considered the same URL. If one URL has the default port 80 and another has no port, they are considered the same URL. If the URLs have the same query parameters and values but in different orders, they are considered the same URL.

Note: you don’t have to implement all conditions of this constraint. Just show that you understand what is required.

## Solution

To solve the problem of checking if a URL already exists in the MySQL table, we can use the following approach:

1. We need to know real-time that URL is exists or not, and if not then we need to insert it. After all the insertion done, we can inform the user about the same.
So we use INSERT IGNORE while inserting the records. So it will avoid duplications ,and also we don't need to perform search query to fund the duplicates.

2. To address the issue of URLs being up to 2048 characters, we can modify the MySQL table to use the `VARCHAR(2048)` data type for the URL column.
And if it's already have a data type text/long text then we can't apply indexing on URL column because given index key prefix length limit is 3072 bytes

3. To ensure that all versions of the same URL match, we can modify the URL before inserting it into the MySQL table by:

1. Removing the scheme (e.g., http, https) from the URL.

2. Removing the default port (e.g., 80/443) from the URL.

3. Sorting the query parameters in alphabetical order before inserting the URL into the MySQL table.

## Modifications to the table

To implement the above solution, we need to add a new column to the table to store the hash values. 
We can add a VARCHAR(32) column to the table to store the hash values and apply unique constraint so that INSERT IGNORE can work. 
We can then create an index on this column to enable fast lookups because for long URLs it would not be possible to apply index on url columns.

## Usage

To use the `URLChecker` class or service:

1. Checkout this branch and perform "composer install"

2. Set your environment variables in .env file

3. Perform migrations

4. Run the application using "symfony:serve"


