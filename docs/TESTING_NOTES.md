# Local smoke-test checklist

Run these checks in the XAMPP browser after importing the database. Record the date, browser, screen width, actual result and evidence in Practical Assessment 2's test log.

1. Home page opens without PHP warnings.
2. Register a buyer using a valid email and password of at least 10 characters.
3. Try invalid email and a short password; the application should reject them.
4. Register a farmer, create a listing, and confirm it is pending.
5. Log in as admin; approve the listing.
6. Log in as buyer; search by produce name and filter by category.
7. Add an available quantity to cart and place an order.
8. Verify stock reduces and order appears in Orders.
9. Update order status through confirmed, dispatched and delivered.
10. Open receipt and use Print → Save as PDF.
11. Try opening farmer/admin pages while logged out or as the wrong role; access should be denied or redirect to login.
12. Attempt SQL injection text in login fields; it must not log in or cause a SQL error.
13. Test at desktop width (1366px) and mobile width (375px) and check navigation/cards/tables.

Do not mark a test as passed until you have run it and recorded the observed result. Take screenshots for evidence.
