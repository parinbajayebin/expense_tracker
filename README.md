# expense_tracker
Fully PHP project with CSS and basic javascript

# 💰 Expense Tracker Web Application

This **Expense Tracker** is a PHP-based web application designed to help users manage their daily finances, track expenses, set budgets, and visualize spending patterns. It uses **MySQL (Regular DB type)** via **phpMyAdmin** for backend data storage and supports user authentication, budgeting, categorization, and reporting.

---

## 🚀 Features

- User Registration & Login
- Add / View / Delete Expenses
- Income Tracking
- Budget Setting
- Expense Category Management
- Monthly Reports
- Data Visualization with Charts
- UPI Transaction Logging (optional)
- Simple and responsive UI (HTML, CSS, JavaScript)

---

## 🛠️ Technologies Used

- **PHP**
- **MySQL (phpMyAdmin)**
- **HTML5**
- **CSS3**
- **JavaScript**
- **Bootstrap (optional)**
- **XAMPP (Apache Server & MySQL)**

---

## 🧰 Requirements

Before you begin, ensure you have the following installed:

- ✅ [XAMPP](https://www.apachefriends.org/index.html) (Includes Apache + MySQL + phpMyAdmin)
- ✅ Web Browser (Chrome, Firefox, Edge)
- ✅ Code Editor (VS Code / Sublime Text / Notepad++)


---

## 🔧 Setup Instructions

1. **Download and Install XAMPP**

   - Run Apache and MySQL from the XAMPP Control Panel.

2. **Clone or Download the Project**

   - Place the project folder `expense_tracker` inside `C:\xampp\htdocs\`

3. **Create Database**

   - Open your browser and go to: `http://localhost/phpmyadmin`
   - Create a new database: `expense_tracker`
   - Import the provided SQL file (`expense_tracker.sql`) from the project root.

4. **Configure Database Connection**

   - In `includes/dbconnect.php`, update your DB credentials if needed:
     ```php
     $conn = mysqli_connect("localhost", "root", "", "expense_tracker");
     ```

5. **Run the Project**

   - Visit `http://localhost/expense_tracker/` in your browser.

---

## 📊 Future Improvements

- Email notifications for budget limits
- Graphical analytics with filters
- Mobile-first responsive design
- Export to Excel/PDF
- Support for recurring expenses

---

## 🤝 Contribution

If you find bugs or want to contribute improvements, feel free to fork the repo or raise issues. Pull requests are welcome!

---

## 📃 License

This project is open-source and available under the [UNLICENSED](LICENSE).

---

## ✍️ Author

Developed with ❤️ by Parin. 
