# Product Performance Checker

A simple PHP web app for tracking product sales by day and viewing weekly performance.

## Overview

This project lets you:

- Record sales for each product on different days
- Reset sales for a selected day
- Commit sales for all products in a day at once
- View weekly average sales and performance rating

## Features

- Product list: Notebook, Ballpen, Pencil, Correction Tape, Ruler
- Daily sales tracking from Monday to Sunday
- Sales value range from 0 to 100
- Performance labels:
  - High Performing
  - Moderate Performing
  - Low Performing
  - Critically Low Performing
- Weekly summary view when all daily data is complete

## Requirements

- PHP installed
- A local web server such as XAMPP, WAMP, or PHP built-in server

## Run the Project

1. Place the project in your web server directory (for example, in `htdocs` for XAMPP).
2. Start Apache.
3. Open the app in your browser:
   - `http://localhost/EDP/EDPE1/EDPExcercise1.php`

## How It Works

- Sales data is stored in the PHP session so it persists while the browser is active.
- Each product can be updated for the selected day using a slider.
- The app calculates weekly averages from all seven days.
- The Weekly option is enabled only when all product-day values are filled.

## Main File

- `EDPExcercise1.php` — main application logic and interface

## Notes

This is a simple learning project and is intended for demonstration and practice of PHP sessions, forms, arrays, and basic web app logic.
