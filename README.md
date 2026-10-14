# TreasureHunt – Online Auction Website (PHP + MySQL)

An eBay-style auction website where users list items, place bids, buy items, and admins view reports and export data to CSV.

**Course:** SWE 6633 Software Project Planning & Management, Kennesaw State University. **Date:** Summer 2025 (final demo July 2025). **Type:** Team project (4 people).

## Overview

The "client" was an auction business that sells items in person and wants to sell online too. Our team built a website for it:

- **Guests** can browse items by category, but must register to buy or sell.
- **Registered users** can list items for auction, bid, use "Buy Now", make an offer, and keep a watchlist.
- **Admins** can see live statistics, view tables, run sales reports, and export data as CSV files.

The course was about project management, so we also wrote a project plan with a 5-week timeline, features, and success criteria (`docs/project-plan.docx`). The final report is in `docs/final-report.pdf`.

## What I did

This was a team project, and I was one of four developers. We did not record which files each person wrote, so I describe the project as a whole. As a team member I took part in:

- Planning the work in weekly steps (design, login, items and bidding, admin and reports, testing).
- Building and testing the website and the MySQL database together with the team.
- Understanding and explaining the database design, the SQL reports, and the CSV export (the parts I focus on below).

## Tech used

- PHP 7+ (server code), with both **PDO** and **mysqli** (two PHP ways to talk to MySQL)
- MySQL / MariaDB through XAMPP and phpMyAdmin
- HTML, CSS, JavaScript, Chart.js (charts on the admin page)

## Database design

The file `treasurehunt.sql` is a phpMyAdmin export of the database. It has 6 tables:

| Table | What it stores | Keys and rules |
|---|---|---|
| Users | accounts | PK `UserID`; **UNIQUE** `Username` |
| Categories | item categories (Electronics, Fashion, ...) | PK `CategoryID`; `ParentCategoryID` → Categories (a category can have a parent) |
| Items | auction listings, start/end time, starting price | PK `ItemID`; FK `CategoryID` → Categories, `SellerID` → Users |
| Bids | each bid on an item | PK `BidID`; FK `ItemID` → Items, `BidderID` → Users |
| Purchases | sold items | PK `PurchaseID`; **UNIQUE** `ItemID` (an item can be sold only once); FK to Items and Users |
| Reports | admin actions | PK `ID`; FK `AdminID` → Users |

Main relationships: one user has many items and many bids; one item has many bids; one category has many items.

**Important note:** this SQL export is from **June 30, 2025**, early in the project. The PHP code was built further after that. It uses some tables and columns that are not in this file, for example `Purchase`, `Admins`, `Watchlist`, `Notifications`, and `Items.Status` / `WinnerID` / `FinalPrice`. So some pages will not work with this file alone.

## Key SQL

Foreign keys from `treasurehunt.sql`. They make sure every bid points to a real item and a real user:

```sql
ALTER TABLE `Bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`BidderID`) REFERENCES `Users` (`UserID`);
```

Admin dashboard numbers with `COUNT` (from `admin_stats.php`). The page sends them as JSON to a Chart.js chart:

```sql
SELECT COUNT(*) AS items_on_sale FROM Items WHERE EndTime > NOW();
SELECT COUNT(*) AS bids_today FROM Bids WHERE DATE(BidTime) = CURDATE();
```

Sales report: items sold on a date in one category, with a 4-table **JOIN** (combining rows from several tables) and a **prepared statement** (the `?` values are sent separately, which protects against SQL injection). From `category_sales_report.php`:

```sql
SELECT i.Name AS ItemName, c.CategoryName, b.BidAmount AS SoldPrice,
       p.PurchaseDate, u.FullName AS BuyerName
FROM Purchase p
JOIN Bids b ON p.BidID = b.BidID
JOIN Items i ON b.ItemID = i.ItemID
JOIN Categories c ON i.CategoryID = c.CategoryID
JOIN Users u ON b.UserID = u.UserID
WHERE DATE(p.PurchaseDate) = ? AND c.CategoryID = ?
```

Finding the winner when an auction ends (from `expire_old_auctions.php`):

```sql
SELECT UserID, BidAmount FROM Bids
WHERE ItemID = :itemId
ORDER BY BidAmount DESC LIMIT 1
```

### Business rules in the code

- **Bid must be higher:** `submit_bid.php` finds `MAX(BidAmount)` for the item and rejects any bid that is not greater than both the starting price and the current highest bid. It also rejects bids after the auction end time.
- **CSV export:** `export_csv.php` only accepts table names from a fixed **allow-list** (a list of permitted values), then writes the column names and rows with PHP `fputcsv`.
- **Passwords** are stored as hashes with `password_hash()` and checked with `password_verify()`.
- **Login** allows 3 failed attempts, then sends the user back to the home page.

## How to run locally

1. Install XAMPP and start **Apache** and **MySQL**.
2. Copy this folder into `xampp/htdocs/` (for example `htdocs/treasure_hunt/`).
3. In phpMyAdmin, create a database named `treasurehunt` and import `treasurehunt.sql`.
4. The code connects as `root` with an empty password (the XAMPP default). See `db.php`.
5. Open `http://localhost/treasure_hunt/TreasureHunt.php` (add your port if Apache is not on 80).

Remember the note above: the SQL file is an early version, so some pages (for example purchases and notifications) need extra tables.

## Known limitations

This is a class project, built for a demo on a local computer. It is not ready for real users.

- Some pages build SQL by joining strings with user input. For example, the search in `table_dump.php` puts the search text directly into a `LIKE` clause. This can allow **SQL injection** (an attack where input changes the query). Prepared statements should be used everywhere.
- The Users table has a `CreditCardInfo` column stored as **plain text**. This is for the demo only. A real site must never store card numbers like this; it should use a payment provider.
- The SQL export does not match the final code (see "Database design").
- The database password is written in the code, and some helper files (`test_admin_login.php`, `generate_admin.php`, `make_hash.php`, `phpinfo.php`) are testing tools that should not be on a live server.
- The code mixes PDO and mysqli, and a few report pages print values without HTML escaping.

## What I learned

- How to plan a software project in weekly steps and work with a team of four.
- How foreign keys and UNIQUE keys protect data quality (for example, one purchase per item).
- How to write reports with JOINs, `COUNT`, `MAX`, and date filters, and how to export results to CSV.
- Why prepared statements matter, and why sensitive data like card numbers needs special care.
- Keeping the database export in sync with the code is part of the job.

## Team

- Mounia Touil
- Shahiba Shamshad
- Michael Butler
- Oluwatomisin Adegbayi
