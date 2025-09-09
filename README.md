# ☕ BrewMaster Cafe System

A premium, full-stack cafe management platform that transforms coffee shop operations. Built with PHP & MySQL, featuring real-time order processing, intelligent inventory management, staff performance analytics, and seamless payment integration. Perfect for modern cafes seeking digital transformation.

![Cafe Management System](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)

## 🌟 Features

### 👥 Role-Based Access Control
- **Customer Portal** - Order placement, profile management, order history
- **Staff Panel** - Order management, payment processing, sales reports, kitchen display
- **Admin Dashboard** - Complete system control, inventory management, staff management, analytics

### 🛒 Customer Features
- **User Registration & Login** - Secure authentication system
- **Menu Browsing** - Interactive menu with categories and search
- **Order Placement** - Real-time cart management with AJAX
- **Order Tracking** - Live status updates (pending → preparing → ready → completed)
- **Profile Management** - Personal information and order history
- **Responsive Design** - Mobile-friendly interface

### 👨‍💼 Staff Features
- **Order Management** - View, update, and process customer orders
- **Payment Processing** - Multiple payment methods (Cash, Card, Mobile Banking)
- **Receipt Generation** - Automatic receipt printing and customer copies
- **Kitchen Display System** - Real-time order tracking with audio notifications
- **Personal Sales Reports** - Individual performance analytics with charts
- **Sales Analytics** - Hourly sales data, payment method breakdown
- **Export Functionality** - CSV export for sales data

### 🔧 Admin Features
- **Staff Management** - Approve/reject staff registrations, manage permissions
- **Product Management** - Add, edit, delete menu items with categories
- **Inventory Management** - Real-time stock tracking with low-stock alerts
- **Stock Movement Tracking** - Complete audit trail for inventory changes
- **Sales Dashboard** - Comprehensive analytics with Chart.js visualizations
- **Reports Generation** - Detailed sales and inventory reports
- **System Configuration** - Global settings and thresholds

### 🎯 Advanced Features
- **Real-time Updates** - AJAX-powered dynamic content updates
- **Chart Analytics** - Interactive charts for sales and inventory data
- **Stock Alerts** - Automated low-stock notifications
- **Audit Trail** - Complete logging of all system activities
- **Print Integration** - Receipt and report printing capabilities
- **Modern UI/UX** - Gradient backgrounds, animations, and responsive design

## 🏗️ Project Structure

```
BrewMaster Cafe System/
├── 📁 admin/                          # Administrator Panel
│   ├── admin_login.php                # Admin authentication
│   ├── admin_logout.php               # Admin logout handler
│   ├── index.php                      # Admin dashboard
│   ├── staff_approval.php             # Staff registration approval
│   ├── staff_management.php           # Staff account management
│   ├── product_management.php         # Menu item management
│   ├── inventory.php                  # Inventory management system
│   ├── update_stock.php               # Stock update processor
│   ├── stock_history.php              # Stock movement history
│   ├── stock_report.php               # Inventory reports
│   └── reports.php                    # Sales analytics dashboard
│
├── 📁 staff/                          # Staff Panel
│   ├── staff_login.php                # Staff authentication
│   ├── staff_logout.php               # Staff logout handler
│   ├── staff_registration.php         # Staff registration form
│   ├── process_staff_registration.php # Registration processor
│   ├── index.php                      # Staff dashboard
│   ├── order_management.php           # Order processing interface
│   ├── payment_processing.php         # Payment handling system
│   ├── kitchen_display.php            # Real-time kitchen orders
│   ├── get_kitchen_orders.php         # Kitchen data API
│   ├── sales_report.php               # Individual sales analytics
│   ├── get_staff_chart_data.php       # Sales chart data API
│   └── export_sales_csv.php           # Sales data export
│
├── 📁 customer/                       # Customer Portal
│   ├── customer_login.php             # Customer authentication
│   ├── customer_logout.php            # Customer logout handler
│   ├── customer_registration.php      # Customer registration form
│   ├── process_customer_registration.php # Registration processor
│   ├── index.php                      # Customer dashboard
│   ├── menu.php                       # Interactive menu browser
│   ├── place_order.php                # Order placement system
│   ├── order_history.php              # Personal order history
│   ├── customer_profile.php           # Profile management
│   └── get_menu_data.php              # Menu data API
│
├── 📁 global/                         # Shared Resources
│   ├── 📁 css/
│   │   └── style.css                  # Comprehensive styling
│   ├── 📁 js/
│   │   └── main.js                    # Global JavaScript utilities
│   ├── 📁 php/
│   │   ├── db_connect.php             # Database connection
│   │   └── cafe_database.sql          # Complete database schema
│   └── 📁 images/                     # Static assets
│
├── index.html                         # Landing page
├── cafe_database.sql                  # Database setup script
├── inventory_setup.sql                # Inventory system setup
└── README.md                          # Project documentation
```

## 🛠️ Technology Stack

| Technology | Purpose | Version |
|------------|---------|---------|
| **PHP** | Backend Logic | 7.4+ |
| **MySQL** | Database | 5.7+ |
| **JavaScript** | Frontend Interactivity | ES6+ |
| **CSS3** | Styling & Animations | Latest |
| **AJAX** | Asynchronous Operations | jQuery/Vanilla |
| **Chart.js** | Data Visualization | 3.x |
| **XAMPP** | Development Environment | Latest |

## 📋 Prerequisites

- **XAMPP/WAMP/LAMP** server environment
- **PHP 7.4** or higher
- **MySQL 5.7** or higher
- **Modern web browser** (Chrome, Firefox, Safari, Edge)
- **Text editor/IDE** (VS Code, Sublime Text, etc.)

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/brewmaster-cafe-system.git
cd brewmaster-cafe-system
```

### 2. Setup XAMPP Environment
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL services
3. Copy project folder to `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux)

### 3. Database Configuration
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Create a new database named `cafe_management`
3. Import the database schema:
   ```sql
   # Navigate to the project directory and import
   mysql -u root -p cafe_management < global/php/cafe_database.sql
   ```
   Or use phpMyAdmin's import feature with `cafe_database.sql`

### 4. Configure Database Connection
Update `global/php/db_connect.php` with your database credentials:
```php
<?php
$servername = "localhost";
$username = "root";          // Your MySQL username
$password = "";              // Your MySQL password
$dbname = "cafe_management"; // Database name
?>
```

### 5. Access the Application
- **Main Landing Page**: `http://localhost/brewmaster-cafe-system/`
- **Customer Portal**: `http://localhost/brewmaster-cafe-system/customer/`
- **Staff Panel**: `http://localhost/brewmaster-cafe-system/staff/`
- **Admin Dashboard**: `http://localhost/brewmaster-cafe-system/admin/`

## 👤 Default Login Credentials

### Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Email**: `admin@cafe.com`

### Test Staff Account
- **Email**: `mike@cafe.com`
- **Password**: `password123`
- **Status**: Approved

### Test Customer Account
- **Email**: `john@example.com`
- **Password**: `password123`

## 🎯 User Roles & Permissions

### 🛡️ Administrator
- ✅ Full system access and control
- ✅ Staff registration approval/rejection
- ✅ Product and menu management
- ✅ Inventory tracking and stock management
- ✅ Sales analytics and reporting
- ✅ System configuration and settings

### 👨‍💼 Staff Member
- ✅ Order management and processing
- ✅ Payment processing (multiple methods)
- ✅ Kitchen display for order tracking
- ✅ Personal sales reports and analytics
- ✅ Customer service functions
- ❌ Cannot access admin functions

### 👤 Customer
- ✅ Menu browsing and ordering
- ✅ Order history and tracking
- ✅ Profile management
- ✅ Real-time order status updates
- ❌ Cannot access staff/admin functions

## 🔧 Configuration Options

### Database Settings
- **Host**: `localhost` (default)
- **Port**: `3306` (default MySQL port)
- **Database**: `cafe_management`

### System Settings
- **Low Stock Threshold**: 10 units (configurable)
- **Session Timeout**: 30 minutes
- **Order Status Flow**: Pending → Confirmed → Preparing → Ready → Completed

### Payment Methods
- **Cash** - Manual change calculation
- **Card** - Credit/Debit card processing
- **bKash** - Mobile banking (Bangladesh)
- **Nagad** - Mobile banking (Bangladesh)
- **Rocket** - Mobile banking (Bangladesh)

## 📊 Features Overview

### Customer Experience
- 🛒 **Shopping Cart** - Real-time cart management
- 🔍 **Search & Filter** - Find products by category/name
- 📱 **Mobile Responsive** - Works on all devices
- 🔔 **Order Notifications** - Real-time status updates

### Staff Operations
- 📋 **Order Queue** - Organized order processing
- 💰 **Payment Processing** - Multiple payment methods
- 🧾 **Receipt Generation** - Automatic receipt printing
- 📈 **Performance Tracking** - Individual sales analytics

### Administrative Control
- 👥 **User Management** - Complete user control
- 📦 **Inventory Control** - Stock tracking with alerts
- 📊 **Business Analytics** - Comprehensive reporting
- 🔧 **System Configuration** - Customizable settings

## 🎨 UI/UX Features

- **Modern Design** - Gradient backgrounds and smooth animations
- **Responsive Layout** - Mobile-first design approach
- **Interactive Elements** - Hover effects and transitions
- **Color-Coded Status** - Visual status indicators
- **Chart Integration** - Beautiful data visualizations
- **Print-Friendly** - Optimized printing layouts

## 🔒 Security Features

- **Password Hashing** - BCrypt encryption
- **SQL Injection Prevention** - Prepared statements
- **Session Management** - Secure session handling
- **Role-Based Access** - Proper permission controls
- **Input Validation** - Client and server-side validation

## 📈 Analytics & Reporting

### Sales Analytics
- Daily/Weekly/Monthly sales trends
- Payment method distribution
- Top-selling products analysis
- Staff performance metrics

### Inventory Reports
- Current stock levels
- Low stock alerts
- Stock movement history
- Inventory valuation

## 🛠️ Development & Customization

### Adding New Features
1. Create new PHP files in appropriate role directories
2. Update navigation menus in respective dashboards
3. Add corresponding CSS styles in `global/css/style.css`
4. Include JavaScript functionality in `global/js/main.js`

### Database Modifications
1. Update schema in `global/php/cafe_database.sql`
2. Create migration scripts for existing installations
3. Update related PHP files for new fields/tables

### Styling Customization
- Modify `global/css/style.css` for visual changes
- Update color schemes in CSS custom properties
- Adjust responsive breakpoints as needed

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🐛 Known Issues & Limitations

- **Email Integration**: Email notifications not implemented
- **Payment Gateway**: Real payment processing not integrated
- **Multi-location**: Single location support only
- **Backup System**: Automated backups not implemented

## 🔮 Future Enhancements

- [ ] Email notification system
- [ ] Real payment gateway integration
- [ ] Multi-location support
- [ ] Mobile app development
- [ ] Advanced reporting dashboard
- [ ] Automated backup system
- [ ] Customer loyalty program
- [ ] Table reservation system

## 📞 Support & Contact

For support, feature requests, or bug reports:
- **Create an Issue**: [GitHub Issues](https://github.com/yourusername/brewmaster-cafe-system/issues)
- **Email**: your-email@example.com
- **Documentation**: Check the wiki for detailed guides

## 🙏 Acknowledgments

- **Chart.js** for beautiful data visualizations
- **Unsplash** for high-quality stock images
- **Font Awesome** for icon sets
- **PHP Community** for excellent documentation and resources

---

<div align="center">

**Made with ❤️ for the cafe industry**

⭐ **Star this repository if you find it helpful!** ⭐

</div>
