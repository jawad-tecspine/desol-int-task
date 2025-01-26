# Employee Data Plugin

A WordPress plugin to manage employee data, including custom post types, salary calculations, and employee lists.

## Installation

1. **Download the Plugin**
   - Download the plugin files as a `.zip` archive or clone the repository.

2. **Install via WordPress Admin**
   - Go to your WordPress admin dashboard.
   - Navigate to `Plugins > Add New`.
   - Click `Upload Plugin` and select the `.zip` file.
   - Click `Install Now` and then `Activate`.

3. **Activate Permalinks**
   - After activation, go to `Settings > Permalinks` and click `Save Changes` to flush rewrite rules.

---

## Features & How to Use

### 1. Employee Post Type
- A custom post type `Employee` is added to the WordPress dashboard.
- Navigate to `Employees > Add New` to add new employee data, including custom fields like:
  - Name
  - Position
  - Email
  - Date of Hire
  - Salary

### 2. Employee List
- Navigate to `Employees > Employee List` in the admin menu.
- Features:
  - Filter employees by date of hire or salary.
  - Sort data in ascending or descending order.
  - Export employee data as a CSV file.

### 3. Salary Average
- Navigate to `Employees > Salary Average`.
- Features:
  - A button to calculate the average salary of all employees.
  - Results are displayed dynamically using AJAX.

---

## Uninstallation

- When the plugin is deleted, all associated custom meta data for employees will also be removed automatically.

---

## Support

For any issues or suggestions, contact the developer.
