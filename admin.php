<?php
require_once 'config.php';
requireLogin();

if (!hasRole('admin')) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('https://img.freepik.com/premium-photo/desk-office-top-view-with-computer-laptop-notepad-pen-coffee-plant-black-background_149391-109.jpg');
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container-fluid {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: auto;
        }
        h3 {
            color: #007bff;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        .back-link {
            display: block;
            margin-bottom: 20px;
            text-align: right;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .modal-dialog {
            max-width: 800px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="back-link">
                    <a href="logout.php">Logout</a>
                </div>
                <h3 class="text-center">Admin Dashboard</h3>
                <div class="btn-group d-flex mb-4" role="group">
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('placement')">Placement</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('internship')">Internship</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('beHonors')">BE Honors</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('additionalCourse')">Additional Courses</button>
                </div>
                <div id="detailsContent">
                    <!-- Details will be dynamically displayed here -->
                </div>
                <div class="action-buttons">
                    <button class="btn btn-warning" onclick="editSelected()">Edit Selected</button>
                    <button class="btn btn-danger" onclick="deleteSelected()">Delete Selected</button>
                    <button class="btn btn-success" onclick="downloadDetails()">Download as CSV</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Record</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <!-- Form fields will be dynamically added here -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveChanges()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentType = '';
        let selectedRows = new Set();
        let editingId = null;

        async function showDetails(type) {
            currentType = type;
            selectedRows.clear();
            
            try {
                const response = await fetch(`get_details.php?type=${type}`);
                const result = await response.json();
                
                const content = document.getElementById('detailsContent');
                
                if (!result.success) {
                    content.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    return;
                }
                
                if (result.data.length === 0) {
                    content.innerHTML = `<div class="alert alert-info">No records found for ${type}.</div>`;
                    return;
                }
                
                let html = '<div class="table-responsive"><table class="table table-bordered"><thead><tr>';
                html += '<th><input type="checkbox" onclick="toggleAllRows(this)"></th>';
                
                // Generate table headers
                const headers = Object.keys(result.data[0]);
                headers.forEach(header => {
                    if (header !== 'id') {
                        html += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
                    }
                });
                html += '</tr></thead><tbody>';
                
                // Generate table rows
                result.data.forEach(row => {
                    html += `<tr data-id="${row.id}">`;
                    html += `<td><input type="checkbox" onclick="toggleRow('${row.id}')"></td>`;
                    headers.forEach(header => {
                        if (header !== 'id') {
                            let value = row[header];
                            if (header.includes('path')) {
                                value = `<a href="uploads/${value}" target="_blank">View File</a>`;
                            }
                            html += `<td>${value}</td>`;
                        }
                    });
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                content.innerHTML = html;
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('detailsContent').innerHTML = 
                    '<div class="alert alert-danger">An error occurred while fetching the details.</div>';
            }
        }

        function toggleRow(id) {
            if (selectedRows.has(id)) {
                selectedRows.delete(id);
            } else {
                selectedRows.add(id);
            }
        }

        function toggleAllRows(checkbox) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const id = row.dataset.id;
                const rowCheckbox = row.querySelector('input[type="checkbox"]');
                rowCheckbox.checked = checkbox.checked;
                if (checkbox.checked) {
                    selectedRows.add(id);
                } else {
                    selectedRows.delete(id);
                }
            });
        }

        async function editSelected() {
            if (selectedRows.size !== 1) {
                alert('Please select exactly one row to edit.');
                return;
            }

            const id = Array.from(selectedRows)[0];
            editingId = id;
            
            try {
                const response = await fetch(`get_details.php?type=${currentType}`);
                const result = await response.json();
                
                if (!result.success) {
                    alert('Error fetching record details');
                    return;
                }
                
                const record = result.data.find(r => r.id === parseInt(id));
                if (!record) {
                    alert('Record not found');
                    return;
                }
                
                // Generate edit form
                let formHtml = '';
                Object.entries(record).forEach(([key, value]) => {
                    if (key !== 'id' && !key.includes('path')) {
                        formHtml += `
                            <div class="form-group">
                                <label for="${key}">${key.replace(/_/g, ' ').toUpperCase()}</label>
                                <input type="text" class="form-control" name="${key}" value="${value}" required>
                            </div>
                        `;
                    }
                });
                
                document.getElementById('editForm').innerHTML = formHtml;
                $('#editModal').modal('show');
                
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while preparing the edit form');
            }
        }

        async function saveChanges() {
            const form = document.getElementById('editForm');
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            try {
                const response = await fetch('admin_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update',
                        type: currentType,
                        id: editingId,
                        data: data
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    $('#editModal').modal('hide');
                    showDetails(currentType);
                    alert('Record updated successfully');
                } else {
                    alert('Error: ' + result.message);
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving changes');
            }
        }

        async function deleteSelected() {
            if (selectedRows.size === 0) {
                alert('Please select rows to delete.');
                return;
            }
            
            if (!confirm('Are you sure you want to delete the selected records?')) {
                return;
            }
            
            try {
                const response = await fetch('admin_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        type: currentType,
                        ids: Array.from(selectedRows)
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    showDetails(currentType);
                    alert('Records deleted successfully');
                } else {
                    alert('Error: ' + result.message);
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting records');
            }
        }

        async function downloadDetails() {
            if (!currentType) {
                alert('Please select a category first.');
                return;
            }
            
            try {
                window.location.href = `download_details.php?type=${currentType}&format=csv`;
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while downloading the file.');
            }
        }
    </script>
</body>
</html>